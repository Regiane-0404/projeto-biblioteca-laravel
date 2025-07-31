<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Cart; // Importamos o nosso modelo Cart
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CartController extends Controller
{
    /**
     * Mostra a página do carrinho de compras.
     */
    public function index()
    {
        dd('Página do Carrinho (index)');
    }

     /* Adiciona um livro ao carrinho.
     */
    public function add(Request $request, Livro $livro)
    {
        // Validação: Não permitir adicionar se não houver stock para venda.
        if ($livro->quantidade_venda <= 0) {
            return back()->with('error', 'Este livro não está mais disponível para venda.');
        }

        // LÓGICA PARA UTILIZADOR LOGADO
        if (Auth::check()) {
            $user = Auth::user();
            // Encontra ou cria um carrinho para o utilizador.
            $cart = $user->cart()->firstOrCreate();

            // Verifica se o livro já existe no carrinho.
            $cartItem = $cart->items()->where('book_id', $livro->id)->first();

            if ($cartItem) {
                // Se existe, apenas incrementa a quantidade.
                $cartItem->increment('quantity');
            } else {
                // Se não existe, cria um novo item no carrinho.
                $cart->items()->create([
                    'book_id' => $livro->id,
                    'quantity' => 1,
                ]);
            }
        }
        // LÓGICA PARA VISITANTE (NÃO LOGADO)
        else {
            // Pega o carrinho da sessão ou um array vazio.
            $cart = session('cart', []);

            if (isset($cart[$livro->id])) {
                // Se o item já existe na sessão, incrementa a quantidade.
                $cart[$livro->id]['quantity']++;
            } else {
                // Se não existe, adiciona o livro à sessão.
                $cart[$livro->id] = [
                    "livro_id" => $livro->id, // Guardamos o ID para referência futura
                    "nome" => $livro->nome,
                    "quantity" => 1,
                    "preco" => (float) $livro->preco, // Guardamos o preço atual
                    "imagem_capa" => $livro->imagem_capa // E a imagem
                ];
            }
            // Guarda o carrinho atualizado de volta na sessão.
            session()->put('cart', $cart);
        }

        // Redireciona para a página anterior com uma mensagem de sucesso.
        return back()->with('success', 'Livro adicionado ao carrinho!');
    }

    /**
     * Atualiza a quantidade de um item no carrinho.
     */
    public function update(Request $request, $itemId)
    {
        dd('Atualizar Item ID: ' . $itemId);
    }

    /**
     * Remove um item do carrinho.
     */
    public function remove($itemId)
    {
        dd('Remover Item ID: ' . $itemId);
    }
}
