<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Encomenda;
use App\Enums\EstadoEncomenda;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Illuminate\Support\Facades\Log; // Para depuração

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        // Vamos precisar desta chave no nosso .env
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\UnexpectedValueException $e) {
            // Payload inválido
            Log::error('Stripe Webhook - Payload inválido: ' . $e->getMessage());
            return response('Payload inválido', 400);
        } catch (SignatureVerificationException $e) {
            // Assinatura inválida
            Log::error('Stripe Webhook - Assinatura inválida: ' . $e->getMessage());
            return response('Assinatura inválida', 400);
        }

        // Lidar com o evento 'checkout.session.completed'
        if ($event->type == 'checkout.session.completed') {
            $session = $event->data->object;

            // Obter o ID da encomenda dos metadados que guardámos
            $encomendaId = $session->metadata->encomenda_id ?? null;

            if ($encomendaId) {
                $encomenda = Encomenda::find($encomendaId);

                // Verificar se a encomenda existe e se ainda está pendente
                if ($encomenda && $encomenda->estado === EstadoEncomenda::PENDENTE) {
                    // ATUALIZAR O ESTADO DA ENCOMENDA
                    $encomenda->estado = EstadoEncomenda::PAGA;
                    $encomenda->save();

                    Log::info("Encomenda #{$encomendaId} marcada como PAGA via webhook.");
                    // Aqui pode adicionar outras lógicas, como:
                    // - Enviar email de confirmação ao cliente
                    // - Notificar a equipa de logística
                }
            } else {
                Log::warning('Stripe Webhook - checkout.session.completed sem encomenda_id nos metadados.', (array)$session);
            }
        }

        // Responder ao Stripe que recebemos o evento com sucesso.
        return response('Webhook Recebido', 200);
    }
}
