<?php

namespace App\Providers;

use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\FluxoDeCaixa;
use App\Models\MDFE;
use App\Models\Motorista;
use App\Models\Pedido;
use App\Models\Plano;
use App\Models\Produto;
use App\Models\User;
use App\Models\Veiculo;
use App\Observers\TransactionObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        Schema::defaultStringLength(191);
        Cliente::observe(TransactionObserver::class);
        Pedido::observe(TransactionObserver::class);
        Plano::observe(TransactionObserver::class);
        Produto::observe(TransactionObserver::class);
        Veiculo::observe(TransactionObserver::class);
        Motorista::observe(TransactionObserver::class);
        MDFE::observe(TransactionObserver::class);
        FluxoDeCaixa::observe(TransactionObserver::class);
    }
}
