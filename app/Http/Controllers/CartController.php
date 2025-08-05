<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{


    // Ficheiro: app/Http/Controllers/CartController.php

    // Ficheiro: app/Http/Controllers/CartController.php

    public function index()
    {
        $cartItems = collect();
        $total = 0;

        if (Auth::check() && Auth::user()->cart) {
            // A CORREÇÃO ESTÁ AQUI: Garante que só carrega itens com livros válidos.
            $cartItems = Auth::user()->cart->items()->whereHas('livro')->with('livro')->get();
        } else {
            $cartFromSession = session('cart', []);
            foreach ($cartFromSession as $id => $details) {
                $cartItems->push((object) [
                    'id' => $id,
                    'quantity' => $details['quantity'],
                    'livro' => (object) ($details['livro_data'] ?? null),
                ]);
            }
        }

        foreach ($cartItems as $item) {
            if ($item->livro && isset($item->livro->preco)) {
                $total += (float)$item->livro->preco * $item->quantity;
            }
        }
        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request, Livro $livro)
    {
        if (($livro->quantidade_venda ?? 0) <= 0) {
            return back()->with('error', 'Este livro não está mais disponível para venda.');
        }

        if (Auth::check()) {
            $user = Auth::user();
            $cart = $user->cart()->firstOrCreate();
            $cartItem = $cart->items()->where('livro_id', $livro->id)->first();

            if ($cartItem) {
                $cartItem->increment('quantity');
            } else {
                $cart->items()->create([
                    'livro_id' => $livro->id,
                    'quantity' => 1
                ]);
            }
        } else {
            $cart = session('cart', []);
            if (isset($cart[$livro->id])) {
                $cart[$livro->id]['quantity']++;
            } else {
                $cart[$livro->id] = [
                    'quantity' => 1,
                    'livro_data' => [
                        'id'          => $livro->id,
                        'nome'        => $livro->nome,
                        'preco'       => (float) $livro->preco,
                        'imagem_capa' => $livro->imagem_capa
                    ]
                ];
            }
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Livro adicionado ao carrinho!');
    }

    public function remove($itemId)
    {
        $cartIsEmpty = false;

        if (Auth::check()) {
            $user = Auth::user();
            if ($user->cart) {
                $user->cart->items()->where('id', $itemId)->delete();
                $cartIsEmpty = $user->cart->items()->count() === 0;
            }
        } else {
            $cart = session('cart', []);
            unset($cart[$itemId]);
            session()->put('cart', $cart);
            $cartIsEmpty = empty($cart);
        }

        if ($cartIsEmpty) {
            return redirect()->route('home')->with('success', 'Seu carrinho está vazio. Continue explorando nossos livros!');
        }

        return back()->with('success', 'Livro removido do carrinho.');
    }
}
