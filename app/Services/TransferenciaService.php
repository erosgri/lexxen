<?php

namespace App\Services;

use App\Models\Carteira;
use App\Models\ContaBancaria;
use App\Models\Transfer;
use App\Models\TransferenciaIdempotencia;
use App\Jobs\ProcessarTransferenciaJob;
use App\Exceptions\TransferenciaException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class TransferenciaService
{
    public function processarTransferenciaEntreCarteiras(array $data): array
    {
        $idempotencyKey = $this->gerarIdempotencyKey($data);
        
        // Verificar se já existe uma transferência com esta chave
        $idempotencia = TransferenciaIdempotencia::where('idempotency_key', $idempotencyKey)->first();
        
        if ($idempotencia) {
            if ($idempotencia->isCompleted()) {
                return [
                    'success' => true,
                    'message' => 'Transferência já processada com sucesso',
                    'idempotency_key' => $idempotencyKey
                ];
            }
            
            if ($idempotencia->isProcessing()) {
                return [
                    'success' => false,
                    'message' => 'Transferência já está sendo processada',
                    'idempotency_key' => $idempotencyKey
                ];
            }
            
            if ($idempotencia->isFailed()) {
                throw TransferenciaException::transferenciaDuplicada($idempotencyKey);
            }
        }

        // Validações iniciais
        $this->validarTransferenciaEntreCarteiras($data);

        // Criar registro de idempotência
        $transferenciaData = array_merge($data, ['tipo' => 'entre_carteiras']);
        $idempotencia = TransferenciaIdempotencia::firstOrCreate(
            ['idempotency_key' => $idempotencyKey],
            [
                'transferencia_data' => $transferenciaData,
                'status' => 'pending'
            ]
        );

        // Processar transferência imediatamente
        try {
            $this->processarTransferenciaImediata($transferenciaData);
            $idempotencia->markAsCompleted();
            
            return [
                'success' => true,
                'message' => 'Transferência realizada com sucesso!',
                'idempotency_key' => $idempotencyKey
            ];
        } catch (\Exception $e) {
            $idempotencia->markAsFailed($e->getMessage());
            throw $e;
        }
    }

    public function processarTransferenciaParaOutros(array $data): array
    {
        $idempotencyKey = $this->gerarIdempotencyKey($data);
        
        // Verificar se já existe uma transferência com esta chave
        $idempotencia = TransferenciaIdempotencia::where('idempotency_key', $idempotencyKey)->first();
        
        if ($idempotencia) {
            if ($idempotencia->isCompleted()) {
                return [
                    'success' => true,
                    'message' => 'Transferência já processada com sucesso',
                    'idempotency_key' => $idempotencyKey
                ];
            }
            
            if ($idempotencia->isProcessing()) {
                return [
                    'success' => false,
                    'message' => 'Transferência já está sendo processada',
                    'idempotency_key' => $idempotencyKey
                ];
            }
            
            if ($idempotencia->isFailed()) {
                throw TransferenciaException::transferenciaDuplicada($idempotencyKey);
            }
        }

        // Validações iniciais
        $this->validarTransferenciaParaOutros($data);

        // Criar registro de idempotência
        $transferenciaData = array_merge($data, ['tipo' => 'para_outros']);
        $idempotencia = TransferenciaIdempotencia::firstOrCreate(
            ['idempotency_key' => $idempotencyKey],
            [
                'transferencia_data' => $transferenciaData,
                'status' => 'pending'
            ]
        );

        // Processar transferência imediatamente
        try {
            $this->processarTransferenciaImediata($transferenciaData);
            $idempotencia->markAsCompleted();
            
            return [
                'success' => true,
                'message' => 'Transferência realizada com sucesso!',
                'idempotency_key' => $idempotencyKey
            ];
        } catch (\Exception $e) {
            $idempotencia->markAsFailed($e->getMessage());
            throw $e;
        }
    }

    protected function validarTransferenciaEntreCarteiras(array $data): void
    {
        $carteiraOrigem = Carteira::find($data['carteira_origem_id']);
        $carteiraDestino = Carteira::find($data['carteira_destino_id']);

        if (!$carteiraOrigem || !$carteiraDestino) {
            throw new Exception('Carteira não encontrada');
        }

        // Verificar se as carteiras pertencem ao mesmo usuário
        if ($carteiraOrigem->owner_id !== $carteiraDestino->owner_id || 
            $carteiraOrigem->owner_type !== $carteiraDestino->owner_type) {
            throw new Exception('As carteiras devem pertencer ao mesmo usuário');
        }

        // Verificar se são carteiras diferentes
        if ($carteiraOrigem->id === $carteiraDestino->id) {
            throw new Exception('As carteiras de origem e destino devem ser diferentes');
        }

        // Validações de negócio
        $this->validarCarteiraOrigem($carteiraOrigem, $data['valor']);
        $this->validarCarteiraDestino($carteiraDestino);
    }

    protected function validarTransferenciaParaOutros(array $data): void
    {
        $carteiraOrigem = Carteira::find($data['carteira_origem_id']);

        if (!$carteiraOrigem) {
            throw new Exception('Carteira de origem não encontrada');
        }

        // Validação da carteira de origem
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
            throw new Exception('Conta de destino não encontrada ou inativa');
        }

        // Buscar o owner da carteira de origem para comparação correta
        $carteiraOrigemOwner = $carteiraOrigem->owner;
        
        // Verificar se não está transferindo para si mesmo
        if ($contaDestino->user_id === $carteiraOrigemOwner->user_id) {
            throw new Exception('Use a opção "Entre Minhas Carteiras" para transferir entre suas próprias carteiras');
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
            ->first();

        if (!$carteiraDestino) {
            throw TransferenciaException::carteiraDefaultNaoEncontrada($userDestino->id);
        }

        // Validação da carteira de destino
        $this->validarCarteiraDestino($carteiraDestino);
    }

    protected function validarCarteiraOrigem(Carteira $carteira, float $valor): void
    {
        // Verificar se o dono da carteira (usuário) está bloqueado
        $user = $carteira->owner->user;
        if ($user && $user->status_aprovacao === 'bloqueado') {
            throw TransferenciaException::contaBloqueada($user->name);
        }

        // Verificar se a carteira está ativa
        if ($carteira->status !== 'ATIVA') {
            throw TransferenciaException::carteiraDesabilitada($carteira->name);
        }

        // Verificar se a carteira está aprovada
        if ($carteira->approval_status !== 'approved') {
            throw TransferenciaException::carteiraDesabilitada($carteira->name);
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
    }

    protected function processarTransferenciaImediata(array $data): void
    {
        if ($data['tipo'] === 'entre_carteiras') {
            $this->processarEntreCarteirasImediata($data);
        } elseif ($data['tipo'] === 'para_outros') {
            $this->processarParaOutrosImediata($data);
        } else {
            throw new Exception("Tipo de transferência inválido: " . ($data['tipo'] ?? 'TIPO_NAO_DEFINIDO'));
        }
    }

    protected function processarEntreCarteirasImediata(array $data): void
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
                'idempotency_key' => $this->gerarIdempotencyKey($data),
            ]);

            // Executar transferência
            $this->executarTransferencia($carteiraOrigem, $carteiraDestino, $data);

            // Marcar transferência como concluída (dispara o Observer)
            $transfer->markAsCompleted();
        });
    }

    protected function processarParaOutrosImediata(array $data): void
    {
        DB::transaction(function () use ($data) {
            // Buscar carteira de origem
            $carteiraOrigemId = $data['carteira_origem_id'] ?? $data['carteira_origem'] ?? null;
            $carteiraOrigem = Carteira::where('id', $carteiraOrigemId)
                ->lockForUpdate()
                ->first();

            if (!$carteiraOrigem) {
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
                'idempotency_key' => $this->gerarIdempotencyKey($data),
            ]);

            // Executar transferência
            $this->executarTransferencia($carteiraOrigem, $carteiraDestino, $data);

            // Marcar transferência como concluída (dispara o Observer)
            $transfer->markAsCompleted();
        });
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

    protected function gerarIdempotencyKey(array $data): string
    {
        // Gerar chave baseada nos dados da transferência
        $keyData = [
            'carteira_origem_id' => $data['carteira_origem_id'],
            'carteira_destino_id' => $data['carteira_destino_id'] ?? null,
            'agencia_destino' => $data['agencia_destino'] ?? null,
            'conta_destino' => $data['conta_destino'] ?? null,
            'valor' => $data['valor'],
            'descricao' => $data['descricao'] ?? '',
            'timestamp' => now()->format('Y-m-d H:i:s')
        ];

        return 'transferencia_' . hash('sha256', json_encode($keyData));
    }

    public function verificarStatusTransferencia(string $idempotencyKey): array
    {
        $idempotencia = TransferenciaIdempotencia::where('idempotency_key', $idempotencyKey)->first();

        if (!$idempotencia) {
            return [
                'found' => false,
                'message' => 'Transferência não encontrada'
            ];
        }

        return [
            'found' => true,
            'status' => $idempotencia->status,
            'message' => $this->getStatusMessage($idempotencia->status),
            'error_message' => $idempotencia->error_message,
            'processed_at' => $idempotencia->processed_at
        ];
    }

    protected function getStatusMessage(string $status): string
    {
        return match($status) {
            'pending' => 'Transferência aguardando processamento',
            'processing' => 'Transferência sendo processada',
            'completed' => 'Transferência processada com sucesso',
            'failed' => 'Transferência falhou',
            default => 'Status desconhecido'
        };
    }
}
