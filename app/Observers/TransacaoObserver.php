<?php

namespace App\Observers;

use App\Models\Carteira;
use App\Models\Transacao;
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
        //
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
