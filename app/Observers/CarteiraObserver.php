<?php

namespace App\Observers;

use App\Models\Carteira;
use Exception;

class CarteiraObserver
{
    /**
     * Handle the Carteira "updating" event.
     */
    public function updating(Carteira $carteira): void
    {
        if ($carteira->isDirty('status') && $carteira->status === 'DESATIVA') {
            if ($carteira->balance > 0) {
                throw new Exception('Não é possível desativar uma carteira com saldo. Por favor, transfira o saldo antes de desativar.');
            }
        }
    }

    /**
     * Handle the Carteira "created" event.
     */
    public function created(Carteira $carteira): void
    {
        //
    }

    /**
     * Handle the Carteira "updated" event.
     */
    public function updated(Carteira $carteira): void
    {
        //
    }

    /**
     * Handle the Carteira "deleted" event.
     */
    public function deleted(Carteira $carteira): void
    {
        //
    }

    /**
     * Handle the Carteira "restored" event.
     */
    public function restored(Carteira $carteira): void
    {
        //
    }

    /**
     * Handle the Carteira "force deleted" event.
     */
    public function forceDeleted(Carteira $carteira): void
    {
        //
    }
}
