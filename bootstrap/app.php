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
        // Adiciona o nosso middleware ao grupo 'web',
        // o que faz com que ele seja executado em quase todos os pedidos.
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\TransferSessionCartToDatabase::class,
        ]);

        // A sua configuração de 'alias' permanece igual.
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'cidadao' => \App\Http\Middleware\CidadaoMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {})->create();
