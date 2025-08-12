<?php

namespace App\Http\Controllers;

use App\Models\Encomenda;
use App\Enums\EstadoEncomenda;
use App\Http\Requests\StoreMoradaRequest;
use Illuminate\Http\Request; // Verifique se este "use" existe
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeCheckoutSession;


class CheckoutController extends Controller
{
    public function mostrarFormularioMorada()
    {
        // ... (este método permanece igual)
        $user = auth()->user();
        if (!$user->cart || $user->cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'O seu carrinho está vazio.');
        }
        $ultimaMorada = $user->moradas()->latest()->first();
        return view('checkout.morada', compact('user', 'ultimaMorada'));
    }

    public function guardarMoradaECriarEncomenda(StoreMoradaRequest $request)
    {
        // ... (este método permanece igual, a redirecionar para a nossa página de pagamento)
        $user = $request->user();
        $cartItems = $user->cart->items;

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'O seu carrinho está vazio.');
        }

        $encomendaCriada = null;

        try {
            DB::transaction(function () use ($user, $cartItems, $request, &$encomendaCriada) {
                $dadosMorada = $request->validated();
                $morada = $user->moradas()->create($dadosMorada);
                $subtotal = $cartItems->sum(fn($item) => $item->quantity * $item->livro->preco);
                $portes_envio = ($subtotal > 50) ? 0 : 4.99;
                $impostos = round($subtotal * 0.06, 2);
                $total = round($subtotal + $portes_envio + $impostos, 2);

                $encomenda = $user->encomendas()->create([
                    'numero_encomenda'    => 'ENC-' . $user->id . '-' . time(),
                    'morada_envio_id'     => $morada->id,
                    'morada_faturacao_id' => $morada->id,
                    'estado'              => EstadoEncomenda::PENDENTE,
                    'subtotal'            => $subtotal,
                    'impostos'            => $impostos,
                    'portes_envio'        => $portes_envio,
                    'total'               => $total,
                ]);

                foreach ($cartItems as $item) {
                    $encomenda->itens()->create([
                        'livro_id'   => $item->livro_id,
                        'quantidade' => $item->quantity,
                        'preco' => $item->livro->preco,
                    ]);
                }
                $user->cart->items()->delete();
                $encomendaCriada = $encomenda;
            });
        } catch (\Exception $e) {
            Log::error('Erro ao criar encomenda: ' . $e->getMessage());
            return back()->with('error', 'Ocorreu um erro inesperado ao processar a sua encomenda.');
        }

        return redirect()->route('checkout.stripe.create', ['encomenda' => $encomendaCriada]);
    }

    public function iniciarSessaoStripe(Encomenda $encomenda)
    {
        // --- Validações de Segurança ---
        if (auth()->id() !== $encomenda->user_id) {
            abort(403, 'Acesso Não Autorizado');
        }
        if ($encomenda->estado !== EstadoEncomenda::PENDENTE) {
            return redirect()->route('dashboard')->with('error', 'Esta encomenda já não pode ser paga.');
        }

        // --- Preparação dos Dados ---
        Stripe::setApiKey(config('services.stripe.secret'));

        // 1. Converter os itens da nossa encomenda para o formato 'line_items' do Stripe.
        $lineItems = [];
        foreach ($encomenda->itens as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency'     => 'eur',
                    'product_data' => [
                        'name'   => $item->livro->nome,
                        'images' => [$item->livro->url_capa], // Usamos o nosso accessor!
                    ],
                    'unit_amount'  => $item->preco * 100, // Valor em cêntimos
                ],
                'quantity'   => $item->quantidade,
            ];
        }

        // 2. Adicionar os portes de envio como um item separado, se existirem.
        if ($encomenda->portes_envio > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency'     => 'eur',
                    'product_data' => ['name' => 'Portes de Envio'],
                    'unit_amount'  => $encomenda->portes_envio * 100,
                ],
                'quantity'   => 1,
            ];
        }

        // 3. (Opcional, mas recomendado) Adicionar os impostos. O Stripe mostra isto como "Tax"
        if ($encomenda->impostos > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency'     => 'eur',
                    'product_data' => ['name' => 'IVA'],
                    'unit_amount'  => $encomenda->impostos * 100,
                ],
                'quantity'   => 1,
            ];
        }


        // --- Criação da Sessão de Checkout ---
        $session = StripeCheckoutSession::create([
            'payment_method_types' => ['card', 'multibanco'],
            'customer_email'       => $encomenda->user->email,
            'line_items'           => $lineItems,
            'mode'                 => 'payment',
            // URLs de retorno que vamos criar no próximo passo.
            'success_url'          => route('checkout.stripe.sucesso'),
            'cancel_url'           => route('checkout.stripe.cancelado'),
            // Metadados para sabermos qual encomenda atualizar no webhook.
            'metadata'             => [
                'encomenda_id' => $encomenda->id,
            ]
        ]);

        // --- Redirecionamento ---
        // Redireciona o browser do utilizador para a página de pagamento do Stripe.
        return redirect()->away($session->url);
    }

    public function sucessoPagamentoStripe(Request $request)
    {
        // Por agora, vamos apenas redirecionar para o dashboard com uma
        // mensagem de sucesso. A atualização real do estado da encomenda
        // será feita pelo Webhook no próximo passo.
        return redirect()->route('dashboard')
            ->with('success', 'Pagamento concluído com sucesso! A sua encomenda está a ser processada.');
    }

    /**
     * Lida com o redirecionamento se o utilizador CANCELAR o pagamento.
     */
    public function canceladoPagamentoStripe()
    {
        // Redirecionamos o utilizador de volta para o carrinho.
        return redirect()->route('cart.index')
            ->with('error', 'O processo de pagamento foi cancelado. O seu carrinho foi restaurado.');
    }
}
