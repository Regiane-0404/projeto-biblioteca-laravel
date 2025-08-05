<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TransferSessionCartToDatabase
{
    public function handle(Request $request, Closure $next): Response
    {
        // Apenas executa a lógica se o utilizador estiver logado
        if (Auth::check()) {
            $sessionCart = session('cart', []);

            // Se houver algo no carrinho da sessão...
            if (!empty($sessionCart)) {
                $user = Auth::user();
                $dbCart = $user->cart()->firstOrCreate();

                foreach ($sessionCart as $livroId => $item) {
                    // Proteção extra para garantir que os dados estão corretos
                    if (!isset($item['quantity'])) {
                        continue;
                    }
                    $quantity = $item['quantity'];

                    $existingItem = $dbCart->items()->where('livro_id', $livroId)->first();
                    if ($existingItem) {
                        $existingItem->increment('quantity', $quantity);
                    } else {
                        $dbCart->items()->create([
                            'livro_id' => $livroId,
                            'quantity' => $quantity,
                        ]);
                    }
                }

                // Limpa a sessão depois de a transferir
                session()->forget('cart');
            }
        }

        return $next($request);
    }
}
