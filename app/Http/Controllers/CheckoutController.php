<?php

namespace App\Http\Controllers;

// --- Imports Atualizados ---
use App\Enums\EstadoEncomenda;
use App\Http\Requests\StoreMoradaRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function mostrarFormularioMorada()
    {
        $user = auth()->user();

        if (!$user->cart || $user->cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'O seu carrinho está vazio.');
        }

        // ALTERAÇÃO: Usa a nova relação 'moradas'
        $ultimaMorada = $user->moradas()->latest()->first();

        return view('checkout.morada', compact('user', 'ultimaMorada'));
    }

    public function guardarMoradaECriarEncomenda(StoreMoradaRequest $request)
    {
        $user = $request->user();
        $cartItems = $user->cart->items;

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'O seu carrinho está vazio.');
        }

        $encomendaCriada = null; // Variável para guardar a encomenda

        try {
            DB::transaction(function () use ($user, $cartItems, $request, &$encomendaCriada) {
                // Validar e guardar a morada
                $dadosMorada = $request->validated();
                $morada = $user->moradas()->create($dadosMorada);

                // --- INÍCIO DAS NOVAS ALTERAÇÕES ---

                // 1. Cálculo do Subtotal (esta parte já existia e estava correta)
                $subtotal = $cartItems->sum(fn($item) => $item->quantity * $item->livro->preco);

                // 2. Lógica de Cálculo dos Portes de Envio (NOVO)
                $portes_envio = 4.99;
                if ($subtotal > 50) {
                    $portes_envio = 0;
                }
                // 3. Lógica de Cálculo de Impostos (IVA a 6%)
                // ALTERAÇÃO AQUI: Adicionado round()
                $impostos = round($subtotal * 0.06, 2);


                // 4. Cálculo do Total Final (NOVO)
                $total = round($subtotal + $portes_envio + $impostos, 2);
                // --- FIM DAS NOVAS ALTERAÇÕES ---


                // Criar a encomenda com os valores calculados
                $encomenda = $user->encomendas()->create([
                    'numero_encomenda'    => 'ENC-' . $user->id . '-' . time(),
                    'morada_envio_id'     => $morada->id,
                    'morada_faturacao_id' => $morada->id,
                    'estado'              => EstadoEncomenda::PENDENTE,
                    // --- CAMPOS ATUALIZADOS ---
                    'subtotal'            => $subtotal,
                    'impostos'            => $impostos,
                    'portes_envio'        => $portes_envio,
                    'total'               => $total,
                ]);

                // Copiar os itens para a encomenda (lógica existente)
                foreach ($cartItems as $item) {
                    Log::info('Inspeccionando o $item do carrinho antes de criar o EncomendaItem', $item->toArray());
                    $encomenda->itens()->create([
                        'livro_id'   => $item->livro_id,
                        'quantidade' => $item->quantity,
                        'preco'      => $item->livro->preco,
                    ]);
                }

                // Esvaziar o carrinho
                $user->cart->items()->delete();

                // Guardar a encomenda criada para usar fora da transação
                $encomendaCriada = $encomenda;
            });
        } catch (\Exception $e) {
            Log::error('Erro ao criar encomenda', [
                'mensagem' => $e->getMessage(),
                'arquivo'  => $e->getFile(),
                'linha'    => $e->getLine(),
            ]);
            return back()->with('error', 'Ocorreu um erro inesperado ao processar a sua encomenda. Por favor, tente novamente.');
        }

        // Ação original: redirecionava para o dashboard. Iremos mudar isto no futuro.
        return redirect()->route('dashboard')->with('success', 'A sua encomenda foi criada com sucesso e está a aguardar pagamento!');
    }
}
