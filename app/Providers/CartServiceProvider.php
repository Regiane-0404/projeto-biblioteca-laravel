<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class CartServiceProvider extends ServiceProvider
{
    // Ficheiro: app/Providers/CartServiceProvider.php

    public function boot(): void
    {
        View::composer('navigation-menu', function ($view) {
            $cartCount = 0;
            if (Auth::check()) {
                $user = Auth::user();
                if ($user->cart) {
                    // A CORREÇÃO ESTÁ AQUI: Conta apenas os itens que têm um livro válido.
                    $cartCount = $user->cart->items()->whereHas('livro')->sum('quantity');
                }
            } else {
                $cart = session('cart', []);
                $cartCount = count($cart); // Para a sessão, a contagem é mais simples.
            }
            $view->with('cartCount', $cartCount);
        });
    }
}
