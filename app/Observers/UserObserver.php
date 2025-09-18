<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if ($user->wasChanged('approval_status') && $user->approval_status === 'approved') {
            $owner = null;
            if ($user->tipo_usuario === 'pessoa_fisica') {
                $owner = $user->pessoaFisica;
            } elseif ($user->tipo_usuario === 'pessoa_juridica') {
                $owner = $user->pessoaJuridica;
            }

            if ($owner) {
                $owner->carteiras()->create([
                    'name' => 'Principal',
                    'balance' => 0,
                    'type' => 'DEFAULT',
                    'status' => 'ATIVA',
                    'approval_status' => 'approved',
                ]);
            }
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
