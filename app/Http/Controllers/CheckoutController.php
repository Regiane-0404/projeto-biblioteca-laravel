<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;

class CheckoutController extends Controller
{


    public function mostrarFormularioMorada()
    {
        // Pega no utilizador autenticado
        $user = auth()->user();

        // Verifica se o carrinho do utilizador está vazio. Se estiver, redireciona-o.
        if (!$user->cart || $user->cart->items()->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'O seu carrinho está vazio.');
        }

        // Procura pela última morada usada pelo utilizador, para pré-preencher
        $ultimaMorada = $user->addresses()->latest()->first();

        // Envia o utilizador e a sua última morada para a view
        return view('checkout.morada', compact('user', 'ultimaMorada'));
    }
}
