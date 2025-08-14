<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login; // O evento de login do Laravel
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        // Verificamos se o utilizador é uma instância do nosso modelo User
        if ($event->user instanceof \App\Models\User) {
            activity()
                ->causedBy($event->user)
                ->log('O utilizador autenticou-se');
        }
    }
}
