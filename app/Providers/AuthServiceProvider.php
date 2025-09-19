<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Carteira;
use App\Models\Extrato;
use App\Models\Transfer;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Gate para verificar se o usuário pode transacionar com uma carteira específica
        Gate::define('transacionar-carteira', function ($user, Carteira $carteira) {
            if ($user->tipo_usuario === 'admin') {
                return true;
            }

            // Acessa o "dono" da carteira (PessoaFisica/Juridica) e depois o usuário associado.
            // Compara se o ID do usuário autenticado é o mesmo do usuário dono da carteira.
            // Esta é a verificação mais direta e segura.
            if (!$carteira->owner || !$carteira->owner->usuario) {
                return false;
            }
            
            return $user->id === $carteira->owner->usuario->id;
        });

        // Gate para verificar se o usuário pode acessar um extrato
        Gate::define('access-extrato', function ($user, Extrato $extrato) {
            if ($user->tipo_usuario === 'admin') {
                return true;
            }

            $owner = $user->tipo_usuario === 'pessoa_fisica' 
                ? $user->pessoaFisica 
                : $user->pessoaJuridica;

            if (!$owner) {
                return false;
            }

            return $owner->carteiras->contains($extrato->carteira);
        });

        // Gate para verificar se o usuário pode acessar uma transferência
        Gate::define('access-transfer', function ($user, Transfer $transfer) {
            if ($user->tipo_usuario === 'admin') {
                return true;
            }

            $owner = $user->tipo_usuario === 'pessoa_fisica' 
                ? $user->pessoaFisica 
                : $user->pessoaJuridica;

            if (!$owner) {
                return false;
            }

            return $owner->carteiras->contains($transfer->carteiraOrigem) ||
                   $owner->carteiras->contains($transfer->carteiraDestino);
        });

        // Gate para verificar se o usuário pode criar carteiras
        Gate::define('create-carteira', function ($user) {
            // Usuário deve estar aprovado e ter perfil (pessoa física ou jurídica)
            return $user->isAprovado() && in_array($user->tipo_usuario, ['pessoa_fisica', 'pessoa_juridica']);
        });

        // Gate para verificar se o usuário pode fazer transferências
        Gate::define('make-transfer', function ($user) {
            return $user->tipo_usuario !== 'admin' && $user->isAprovado();
        });

        // Gate para verificar se o usuário pode acessar extratos
        Gate::define('view-extrato', function ($user) {
            return $user->tipo_usuario !== 'admin' && $user->isAprovado();
        });

        // Gate para administradores
        Gate::define('admin', function ($user) {
            return $user->tipo_usuario === 'admin';
        });

        // Gate para usuários aprovados
        Gate::define('approved-user', function ($user) {
            return $user->isAprovado();
        });
    }
}
