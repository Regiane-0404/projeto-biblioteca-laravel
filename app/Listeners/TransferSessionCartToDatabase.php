<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Cart;

class TransferSessionCartToDatabase
{
    public function handle(Login $event): void
    {
        $cartFromSession = session('cart', []);
        if (empty($cartFromSession)) return;

        $user = $event->user;
        $dbCart = Cart::firstOrCreate(['user_id' => $user->id]);

        foreach ($cartFromSession as $livroId => $details) {
            // updateOrCreate é a forma mais segura de fazer a fusão
            $dbCart->items()->updateOrCreate(
                [
                    // Condições para encontrar o item:
                    'cart_id' => $dbCart->id,
                    'livro_id' => $livroId,
                ],
                [
                    // Valores a inserir/atualizar:
                    'quantity' => $details['quantity'],
                ]
            );
        }
        session()->forget('cart');
    }
}
