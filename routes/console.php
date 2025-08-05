<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use App\Listeners\TransferSessionCartToDatabase;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// =========================================================
// == LINHA ADICIONADA PARA "LIGAR" O EVENTO DE LOGIN     ==
// =========================================================
Event::listen(Login::class, TransferSessionCartToDatabase::class);