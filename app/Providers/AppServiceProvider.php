<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // =======================================================
        // ==                 INÍCIO DA CORREÇÃO                ==
        // =======================================================
        // Força o fuso horário de toda a aplicação para o que está
        // definido no ficheiro de configuração.
        date_default_timezone_set(config('app.timezone'));
        // =======================================================
        // ==                   FIM DA CORREÇÃO                 ==
        // =======================================================
    }
}
