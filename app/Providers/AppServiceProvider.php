<?php

namespace App\Providers;

use App\Models\Transacao;
use App\Observers\TransacaoObserver;
use App\Models\Carteira;
use App\Observers\CarteiraObserver;
use App\Models\User;
use App\Observers\UserObserver;
use App\Models\Transfer;
use App\Observers\TransferObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Transacao::observe(TransacaoObserver::class);
        Carteira::observe(CarteiraObserver::class);
        User::observe(UserObserver::class);
        Transfer::observe(TransferObserver::class);

        Paginator::useBootstrapFive();
    }
}
