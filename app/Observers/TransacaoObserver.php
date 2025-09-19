<?php

namespace App\Observers;

use App\Models\Carteira;
use App\Models\Transacao;
use App\Models\Extrato;
use Exception;

class TransacaoObserver
{
    /**
     * Handle the Transacao "creating" event.
     */
    public function creating(Transacao $transacao): void
    {
        if ($transacao->tipo === 'credit') {
            $carteira = Carteira::find($transacao->conta_id);

            if ($carteira && $carteira->status === 'DESATIVA') {
                throw new Exception('A carteira de destino está desativada e não pode receber dinheiro.');
            }
        }
    }

    /**
     * Handle the Transacao "created" event.
     */
    public function created(Transacao $transacao): void
    {
        // Criar registro no Extrato
        $carteira = Carteira::find($transacao->conta_id);
        
        if ($carteira) {
            // Determinar tipo de operação baseado no tipo da transação
            $tipoOperacao = $this->determinarTipoOperacao($transacao);
            
            Extrato::create([
                'carteira_id' => $transacao->conta_id,
                'tipo_operacao' => $tipoOperacao,
                'valor' => $transacao->valor,
                'saldo_apos_operacao' => $carteira->balance,
                'conta_origem' => $transacao->tipo === 'debit' ? $carteira->name : null,
                'conta_destino' => $transacao->tipo === 'credit' ? $carteira->name : null,
                'descricao' => $transacao->descricao,
                'data_operacao' => $transacao->created_at,
            ]);
        }
    }
    
    /**
     * Determina o tipo de operação baseado no tipo da transação
     */
    private function determinarTipoOperacao(Transacao $transacao): string
    {
        if ($transacao->tipo === 'credit') {
            if (str_contains($transacao->descricao, 'Transferência recebida')) {
                return 'transferencia_destino';
            }
            return 'deposito';
        } else {
            if (str_contains($transacao->descricao, 'Transferência para')) {
                return 'transferencia_origem';
            }
            return 'saque';
        }
    }

    /**
     * Handle the Transacao "updated" event.
     */
    public function updated(Transacao $transacao): void
    {
        //
    }

    /**
     * Handle the Transacao "deleted" event.
     */
    public function deleted(Transacao $transacao): void
    {
        //
    }

    /**
     * Handle the Transacao "restored" event.
     */
    public function restored(Transacao $transacao): void
    {
        //
    }

    /**
     * Handle the Transacao "force deleted" event.
     */
    public function forceDeleted(Transacao $transacao): void
    {
        //
    }
}
