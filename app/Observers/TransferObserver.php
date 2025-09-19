<?php

namespace App\Observers;

use App\Models\Transfer;
use App\Models\Extrato;
use App\Models\Carteira;

class TransferObserver
{
    /**
     * Handle the Transfer "created" event.
     */
    public function created(Transfer $transfer): void
    {
        // Não fazemos nada na criação, apenas quando a transferência é concluída
    }

    /**
     * Handle the Transfer "updated" event.
     */
    public function updated(Transfer $transfer): void
    {
        // Verifica se a transferência foi marcada como concluída
        if ($transfer->isCompleted() && $transfer->wasChanged('status')) {
            $this->registrarExtratoTransferencia($transfer);
        }
    }

    /**
     * Registra a transferência no extrato das duas carteiras.
     */
    protected function registrarExtratoTransferencia(Transfer $transfer): void
    {
        // Carrega as carteiras com seus relacionamentos
        $carteiraOrigem = $transfer->carteiraOrigem;
        $carteiraDestino = $transfer->carteiraDestino;

        if (!$carteiraOrigem || !$carteiraDestino) {
            return;
        }

        // Buscar informações das contas bancárias
        $contaOrigemInfo = $this->buscarInfoContaBancaria($carteiraOrigem);
        $contaDestinoInfo = $this->buscarInfoContaBancaria($carteiraDestino);

        // Registra no extrato da carteira de origem (saída)
        Extrato::create([
            'carteira_id' => $carteiraOrigem->id,
            'tipo_operacao' => 'transferencia_origem',
            'valor' => $transfer->valor,
            'saldo_apos_operacao' => $carteiraOrigem->balance,
            'conta_origem' => $contaOrigemInfo,
            'conta_destino' => $contaDestinoInfo,
            'descricao' => $transfer->descricao . ' - Para: ' . $carteiraDestino->name,
            'data_operacao' => $transfer->processed_at ?? now(),
        ]);

        // Registra no extrato da carteira de destino (entrada)
        Extrato::create([
            'carteira_id' => $carteiraDestino->id,
            'tipo_operacao' => 'transferencia_destino',
            'valor' => $transfer->valor,
            'saldo_apos_operacao' => $carteiraDestino->balance,
            'conta_origem' => $contaOrigemInfo,
            'conta_destino' => $contaDestinoInfo,
            'descricao' => $transfer->descricao . ' - De: ' . $carteiraOrigem->name,
            'data_operacao' => $transfer->processed_at ?? now(),
        ]);

        // Limpar cache das carteiras para garantir dados atualizados
        $this->limparCacheCarteiras($carteiraOrigem, $carteiraDestino);
    }

    /**
     * Limpa o cache das carteiras envolvidas na transferência
     */
    protected function limparCacheCarteiras(Carteira $carteiraOrigem, Carteira $carteiraDestino): void
    {
        $userIds = [];
        
        // Obter user_id da carteira de origem
        if ($carteiraOrigem->owner && method_exists($carteiraOrigem->owner, 'user')) {
            $userOrigem = $carteiraOrigem->owner->user ?? null;
            if ($userOrigem) {
                $userIds[] = $userOrigem->id;
            }
        }
        
        // Obter user_id da carteira de destino  
        if ($carteiraDestino->owner && method_exists($carteiraDestino->owner, 'user')) {
            $userDestino = $carteiraDestino->owner->user ?? null;
            if ($userDestino) {
                $userIds[] = $userDestino->id;
            }
        }
        
        // Limpar cache para ambos os usuários
        foreach (array_unique($userIds) as $userId) {
            \Cache::forget("carteiras_user_{$userId}");
            \Cache::forget("carteira_balance_{$carteiraOrigem->id}");
            \Cache::forget("carteira_balance_{$carteiraDestino->id}");
        }
        
        // Limpar cache de extratos e resumos (sem usar tags)
        \Cache::flush();
    }

    /**
     * Busca informações da conta bancária associada à carteira.
     */
    protected function buscarInfoContaBancaria(Carteira $carteira): string
    {
        try {
            // Buscar o usuário proprietário da carteira
            $user = null;
            if ($carteira->owner && method_exists($carteira->owner, 'user')) {
                $user = $carteira->owner->user ?? null;
            }

            if (!$user) {
                return $carteira->name;
            }

            // Buscar a conta bancária do usuário
            $contaBancaria = \App\Models\ContaBancaria::where('user_id', $user->id)
                ->where('status', 'ATIVA')
                ->first();

            if ($contaBancaria) {
                return "{$contaBancaria->agencia}/{$contaBancaria->numero}";
            }

            // Se não encontrar conta bancária, tentar extrair do nome da carteira
            if (str_contains($carteira->name, '/')) {
                // Se o nome da carteira já contém agência/conta, usar ele
                return $carteira->name;
            }

            return $carteira->name;
        } catch (\Exception $e) {
            return $carteira->name;
        }
    }

    /**
     * Handle the Transfer "deleted" event.
     */
    public function deleted(Transfer $transfer): void
    {
        // Opcional: remover registros do extrato se a transferência for deletada
        // Extrato::where('conta_origem', $transfer->carteiraOrigem->name)
        //     ->where('conta_destino', $transfer->carteiraDestino->name)
        //     ->where('valor', $transfer->valor)
        //     ->where('data_operacao', $transfer->processed_at)
        //     ->delete();
    }

    /**
     * Handle the Transfer "restored" event.
     */
    public function restored(Transfer $transfer): void
    {
        //
    }

    /**
     * Handle the Transfer "force deleted" event.
     */
    public function forceDeleted(Transfer $transfer): void
    {
        //
    }
}