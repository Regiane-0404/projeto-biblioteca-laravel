<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Encomenda;
use App\Enums\EstadoEncomenda;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sig_header,
                $secret
            );
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe Webhook: Payload inválido.');
            return response('Invalid payload', 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe Webhook: Assinatura inválida.');
            return response('Invalid signature', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $encomendaId = $session->metadata->encomenda_id ?? null;

            if ($encomendaId) {
                $encomenda = Encomenda::find($encomendaId);

                if ($encomenda && $encomenda->estado->value === 'pendente') {
                    $encomenda->estado = EstadoEncomenda::PAGO;
                    $encomenda->save();

                    Log::info("Webhook recebido: A encomenda #{$encomendaId} foi marcada como PAGA.");
                } else {
                    Log::warning("Webhook Stripe: Encomenda {$encomendaId} não encontrada ou já atualizada.");
                }
            } else {
                Log::warning('Webhook Stripe: Nenhum encomenda_id encontrado nos metadados.');
            }
        }

        return response('Webhook Recebido', 200);
    }
}
