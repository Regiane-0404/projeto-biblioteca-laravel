<?php

namespace App\Mail;

use App\Models\Requisicao;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LembreteDevolucao extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * A instância da requisição para a qual o lembrete se destina.
     */
    public Requisicao $requisicao;

    /**
     * Cria uma nova instância da mensagem.
     */
    public function __construct(Requisicao $requisicao)
    {
        $this->requisicao = $requisicao;
    }

    /**
     * Obtém o envelope da mensagem (o assunto).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Lembrete de Devolução da sua Requisição',
        );
    }

    /**
     * Obtém a definição do conteúdo da mensagem (a view).
     */
    public function content(): Content
    {
        // Vamos criar esta view a seguir
        return new Content(
            markdown: 'emails.requisicoes.lembrete',
        );
    }
}
