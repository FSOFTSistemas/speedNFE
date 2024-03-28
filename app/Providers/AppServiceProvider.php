<?php

namespace App\Providers;

use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\MDFE;
use App\Models\Motorista;
use App\Models\Pedido;
use App\Models\Plano;
use App\Models\Produto;
use App\Models\TransactionLog;
use App\Models\User;
use App\Models\Veiculo;
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
        //
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
        Cliente::observe(TransactionLog::class);
        Empresa::observe(TransactionLog::class);
        Pedido::observe(TransactionLog::class);
        Plano::observe(TransactionLog::class);
        Produto::observe(TransactionLog::class);
        User::observe(TransactionLog::class);
        Veiculo::observe(TransactionLog::class);
        Motorista::observe(TransactionLog::class);
        MDFE::observe(TransactionLog::class);
    }
}
