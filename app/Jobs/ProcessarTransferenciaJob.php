<?php

namespace App\Jobs;

use App\Models\Carteira;
use App\Models\ContaBancaria;
use App\Models\Transfer;
use App\Models\TransferenciaIdempotencia;
use App\Exceptions\TransferenciaException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ProcessarTransferenciaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 60;
    public $tries = 3;

    protected string $idempotencyKey;
    protected array $transferenciaData;

    public function __construct(string $idempotencyKey, array $transferenciaData)
    {
        $this->idempotencyKey = $idempotencyKey;
        $this->transferenciaData = $transferenciaData;
    }

    public function handle(): void
    {
        $idempotencia = TransferenciaIdempotencia::where('idempotency_key', $this->idempotencyKey)->first();

        if (!$idempotencia) {
            Log::error("Transferência não encontrada para processamento", [
                'idempotency_key' => $this->idempotencyKey
            ]);
            return;
        }

        if ($idempotencia->isCompleted()) {
            Log::info("Transferência já processada", [
                'idempotency_key' => $this->idempotencyKey
            ]);
            return;
        }

        if ($idempotencia->isProcessing()) {
            Log::warning("Transferência já está sendo processada", [
                'idempotency_key' => $this->idempotencyKey
            ]);
            return;
        }

        $idempotencia->markAsProcessing();

        try {
            $this->processarTransferencia($idempotencia);
            $idempotencia->markAsCompleted();
            
            Log::info("Transferência processada com sucesso", [
                'idempotency_key' => $this->idempotencyKey,
                'tipo' => $this->transferenciaData['tipo']
            ]);

        } catch (Exception $e) {
            $idempotencia->markAsFailed($e->getMessage());
            
            Log::error("Erro ao processar transferência", [
                'idempotency_key' => $this->idempotencyKey,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    protected function processarTransferencia(TransferenciaIdempotencia $idempotencia): void
    {
        $data = $this->transferenciaData;
        
        if ($data['tipo'] === 'entre_carteiras') {
            $this->processarEntreCarteiras($data);
        } elseif ($data['tipo'] === 'para_outros') {
            $this->processarParaOutros($data);
        } else {
            throw new Exception("Tipo de transferência inválido: " . ($data['tipo'] ?? 'TIPO_NAO_DEFINIDO'));
        }
    }

    protected function processarEntreCarteiras(array $data): void
    {
        DB::transaction(function () use ($data) {
            // Lock das carteiras para evitar concorrência
            $carteiraOrigemId = $data['carteira_origem_id'] ?? $data['carteira_origem'] ?? null;
            $carteiraOrigem = Carteira::where('id', $carteiraOrigemId)
                ->lockForUpdate()
                ->first();

            $carteiraDestinoId = $data['carteira_destino_id'] ?? $data['carteira_destino'] ?? null;
            $carteiraDestino = Carteira::where('id', $carteiraDestinoId)
                ->lockForUpdate()
                ->first();

            if (!$carteiraOrigem || !$carteiraDestino) {
                throw new Exception("Carteira não encontrada");
            }

            // Validações de negócio
            $this->validarCarteiraOrigem($carteiraOrigem, $data['valor']);
            $this->validarCarteiraDestino($carteiraDestino);

            // Criar registro de transferência
            $transfer = Transfer::create([
                'carteira_origem_id' => $carteiraOrigem->id,
                'carteira_destino_id' => $carteiraDestino->id,
                'valor' => $data['valor'],
                'descricao' => $data['descricao'] ?? 'Transferência entre carteiras',
                'status' => 'pending',
                'idempotency_key' => $this->idempotencyKey,
            ]);

            // Processar transferência
            $this->executarTransferencia($carteiraOrigem, $carteiraDestino, $data);

            // Marcar transferência como concluída (dispara o Observer)
            $transfer->markAsCompleted();
        });
    }

    protected function processarParaOutros(array $data): void
    {
        DB::transaction(function () use ($data) {
            // Lock da carteira de origem
            $carteiraOrigemId = $data['carteira_origem_id'] ?? $data['carteira_origem'] ?? null;
            $carteiraOrigem = Carteira::where('id', $carteiraOrigemId)
                ->lockForUpdate()
                ->first();

            if (!$carteiraOrigem) {
                \Log::error("Carteira de origem não encontrada", [
                    'carteira_origem_id_buscado' => $carteiraOrigemId,
                    'dados_recebidos' => $data
                ]);
                throw new Exception("Carteira de origem não encontrada");
            }

            // Validações de negócio
            $this->validarCarteiraOrigem($carteiraOrigem, $data['valor']);

            // Buscar conta de destino (tolerante a formatos com/sem hífen e espaços)
            $agenciaInformada = trim((string) ($data['agencia_destino'] ?? ''));
            $numeroInformadoOriginal = trim((string) ($data['conta_destino'] ?? ''));
            $numeroInformadoDigits = preg_replace('/\D+/', '', $numeroInformadoOriginal);

            $contaDestino = ContaBancaria::where('agencia', $agenciaInformada)
                ->where(function ($q) use ($numeroInformadoOriginal, $numeroInformadoDigits) {
                    $q->where('numero', $numeroInformadoOriginal)
                      // compara também desconsiderando o hífen
                      ->orWhereRaw("REPLACE(numero, '-', '') = ?", [$numeroInformadoDigits]);
                })
                ->where('status', 'ATIVA')
                ->first();

            if (!$contaDestino) {
                throw new Exception("Conta de destino não encontrada ou inativa");
            }

            // Buscar carteira DEFAULT do usuário de destino
            $userDestino = $contaDestino->user;
            $ownerDestino = $userDestino->tipo_usuario === 'pessoa_fisica' 
                ? $userDestino->pessoaFisica 
                : $userDestino->pessoaJuridica;

            if (!$ownerDestino) {
                throw TransferenciaException::carteiraDefaultNaoEncontrada($userDestino->id);
            }

            $carteiraDestino = $ownerDestino->carteiras()
                ->where('type', 'DEFAULT')
                ->where('status', 'ATIVA')
                ->where('approval_status', 'approved')
                ->lockForUpdate()
                ->first();

            if (!$carteiraDestino) {
                throw TransferenciaException::carteiraDefaultNaoEncontrada($userDestino->id);
            }

            // Validação da carteira de destino
            $this->validarCarteiraDestino($carteiraDestino);

            // Criar registro de transferência
            $transfer = Transfer::create([
                'carteira_origem_id' => $carteiraOrigem->id,
                'carteira_destino_id' => $carteiraDestino->id,
                'valor' => $data['valor'],
                'descricao' => $data['descricao'] ?? 'Transferência para outro usuário',
                'status' => 'pending',
                'idempotency_key' => $this->idempotencyKey,
            ]);

            // Processar transferência
            $this->executarTransferencia($carteiraOrigem, $carteiraDestino, $data);

            // Marcar transferência como concluída (dispara o Observer)
            $transfer->markAsCompleted();
        });
    }

    protected function validarCarteiraOrigem(Carteira $carteira, float $valor): void
    {
        // Verificar se a carteira está ativa
        if ($carteira->status !== 'ATIVA') {
            throw TransferenciaException::carteiraDesabilitada($carteira->name);
        }

        // Verificar se a carteira está aprovada
        if ($carteira->approval_status !== 'approved') {
            throw TransferenciaException::carteiraDesabilitada($carteira->name);
        }

        // Verificar status da conta bancária
        $user = $carteira->owner->user;
        if ($user) {
            // Verificar se o usuário tem alguma conta bancária bloqueada
            $contaBloqueada = $user->contasBancarias()->where('status', 'BLOQUEADA')->first();
            if ($contaBloqueada) {
                throw TransferenciaException::contaBancariaBloqueada($contaBloqueada->numero);
            }
            
            // Verificar se o usuário tem alguma conta bancária aguardando aprovação
            $contaAguardando = $user->contasBancarias()->where('status', 'AGUARDANDO_APROVACAO')->first();
            if ($contaAguardando) {
                throw TransferenciaException::contaBancariaAguardandoAprovacao($contaAguardando->numero);
            }
        }

        // Verificar saldo suficiente
        if ($carteira->balance < $valor) {
            throw TransferenciaException::saldoInsuficiente($carteira->balance, $valor);
        }
    }

    protected function validarCarteiraDestino(Carteira $carteira): void
    {
        // Verificar se a carteira está ativa
        if ($carteira->status !== 'ATIVA') {
            throw TransferenciaException::carteiraDestinoDesabilitada($carteira->name);
        }

        // Verificar se a carteira está aprovada
        if ($carteira->approval_status !== 'approved') {
            throw TransferenciaException::carteiraDestinoDesabilitada($carteira->name);
        }

        // Verificar status da conta bancária
        $user = $carteira->owner->user;
        if ($user) {
            // Verificar se o usuário tem alguma conta bancária bloqueada
            $contaBloqueada = $user->contasBancarias()->where('status', 'BLOQUEADA')->first();
            if ($contaBloqueada) {
                throw TransferenciaException::contaBancariaDestinoBloqueada($contaBloqueada->numero);
            }
            
            // Verificar se o usuário tem alguma conta bancária aguardando aprovação
            $contaAguardando = $user->contasBancarias()->where('status', 'AGUARDANDO_APROVACAO')->first();
            if ($contaAguardando) {
                throw TransferenciaException::contaBancariaDestinoAguardandoAprovacao($contaAguardando->numero);
            }
        }
    }

    protected function executarTransferencia(Carteira $carteiraOrigem, Carteira $carteiraDestino, array $data): void
    {
        $valor = $data['valor'];
        $descricao = $data['descricao'] ?? 'Transferência processada';

        // Debita da carteira de origem
        $carteiraOrigem->balance -= $valor;
        $carteiraOrigem->save();

        $carteiraOrigem->transacoes()->create([
            'tipo' => 'debit',
            'valor' => $valor,
            'descricao' => $descricao . ' - Para: ' . $carteiraDestino->name,
        ]);

        // Credita na carteira de destino
        $carteiraDestino->balance += $valor;
        $carteiraDestino->save();

        $carteiraDestino->transacoes()->create([
            'tipo' => 'credit',
            'valor' => $valor,
            'descricao' => $descricao . ' - De: ' . $carteiraOrigem->name,
        ]);

        // Limpar cache das carteiras para garantir dados atualizados
        $this->limparCacheCarteiras($carteiraOrigem, $carteiraDestino);
    }

    public function failed(Exception $exception): void
    {
        Log::error("Job de transferência falhou definitivamente", [
            'idempotency_key' => $this->idempotencyKey,
            'error' => $exception->getMessage()
        ]);
    }

    /**
     * Limpa o cache das carteiras envolvidas na transferência
     */
    protected function limparCacheCarteiras(Carteira $carteiraOrigem, Carteira $carteiraDestino): void
    {
        $userIds = [];
        
        // Obter user_id da carteira de origem
        if ($carteiraOrigem->owner && $carteiraOrigem->owner->user) {
            $userIds[] = $carteiraOrigem->owner->user->id;
        }
        
        // Obter user_id da carteira de destino
        if ($carteiraDestino->owner && $carteiraDestino->owner->user) {
            $userIds[] = $carteiraDestino->owner->user->id;
        }
        
        // Limpar cache para ambos os usuários
        foreach (array_unique($userIds) as $userId) {
            \Cache::forget("carteiras_user_{$userId}");
            \Cache::forget("extratos_user_{$userId}_*");
            \Cache::forget("resumo_user_{$userId}_*");
            \Cache::forget("carteira_balance_{$carteiraOrigem->id}");
            \Cache::forget("carteira_balance_{$carteiraDestino->id}");
        }
    }
}