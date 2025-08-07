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

        try {
            DB::transaction(function () use ($user, $cartItems, $request) {
                // Validar e guardar a morada
                $dadosMorada = $request->validated();
                // ALTERAÇÃO: Usa a nova relação 'moradas'
                $morada = $user->moradas()->create($dadosMorada);

                // Calcular o total
                $subtotal = $cartItems->sum(fn($item) => $item->quantidade * $item->livro->preco);
                $total = $subtotal;

                // Criar a encomenda
                // ALTERAÇÃO: Usa a nova relação 'encomendas' e o novo Enum
                $encomenda = $user->encomendas()->create([
                    'numero_encomenda'    => 'ENC-' . $user->id . '-' . time(),
                    // ALTERAÇÃO: Nomes das colunas da BD
                    'morada_envio_id'     => $morada->id,
                    'morada_faturacao_id' => $morada->id,
                    'estado'              => EstadoEncomenda::PENDENTE,
                    'subtotal'            => $subtotal,
                    'impostos'            => 0,
                    'portes_envio'        => 0,
                    'total'               => $total,
                ]);

                // Copiar os itens para a encomenda
                foreach ($cartItems as $item) {


                    // ***** LINHA DE DEBUG ADICIONADA *****
                    Log::info('Inspeccionando o $item do carrinho antes de criar o EncomendaItem', $item->toArray());


                    // ALTERAÇÃO: Usa a nova relação 'itens' do modelo Encomenda
                    $encomenda->itens()->create([
                        'livro_id'   => $item->livro_id,
                        'quantidade' => $item->quantity,
                        'preco'      => $item->livro->preco,
                    ]);
                }

                // Esvaziar o carrinho
                $user->cart->items()->delete();
            });
        } catch (\Exception $e) {
            Log::error('Erro ao criar encomenda', [
                'mensagem' => $e->getMessage(),
                'arquivo'  => $e->getFile(),
                'linha'    => $e->getLine(),
            ]);
            return back()->with('error', 'Ocorreu um erro inesperado ao processar a sua encomenda. Por favor, tente novamente.');
        }

        // Redirecionar com sucesso
        return redirect()->route('dashboard')->with('success', 'A sua encomenda foi criada com sucesso e está a aguardar pagamento!');
    }
}
