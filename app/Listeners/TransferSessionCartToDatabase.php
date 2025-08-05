<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TransferSessionCartToDatabase
{
    public function handle(Login $event): void
    {
        $cartFromSession = session('cart', []);
        if (empty($cartFromSession)) {
            return; // Sai se o carrinho da sessão estiver vazio
        }

        $user = $event->user;
        $dbCart = $user->cart()->firstOrCreate();

        foreach ($cartFromSession as $livroId => $itemData) {
            // VERIFICAÇÃO DE SEGURANÇA (SUGESTÃO DO CHATGPT)
            // Garante que o item tem os dados mínimos necessários.
            if (!isset($itemData['quantity'])) {
                continue; // Ignora este item e passa para o próximo
            }

            $quantity = $itemData['quantity'];

            // A lógica de fusão
            $existingItem = $dbCart->items()->where('livro_id', $livroId)->first();
            if ($existingItem) {
                $existingItem->increment('quantity', $quantity);
            } else {
                $dbCart->items()->create([
                    'livro_id' => $livroId, // Usa a chave do array, que é o ID do livro
                    'quantity' => $quantity,
                ]);
            }
        }

        // Limpa a sessão depois de processar todos os itens válidos
        session()->forget('cart');
    }
}
