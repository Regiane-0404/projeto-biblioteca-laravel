<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // =======================================================
        // ==            INÍCIO DA NOVA CONFIGURAÇÃO            ==
        // =======================================================
        // Diz ao Laravel para não verificar o CSRF na nossa rota do Stripe.
        $middleware->validateCsrfTokens(except: [
            '/stripe/webhook'
        ]);
        // =======================================================
        // ==              FIM DA NOVA CONFIGURAÇÃO             ==
        // =======================================================


        // A sua configuração existente permanece igual.
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\TransferSessionCartToDatabase::class,
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'cidadao' => \App\Http\Middleware\CidadaoMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {})->create();
