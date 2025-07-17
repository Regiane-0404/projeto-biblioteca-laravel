<?php

namespace App\Mail;

use App\Models\Requisicao;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NovaRequisicaoParaAdmin extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Requisicao $requisicao;

    public function __construct(Requisicao $requisicao)
    {
        $this->requisicao = $requisicao;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nova Requisição Pendente de Aprovação: #' . $this->requisicao->numero_sequencial,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.requisicoes.notificacao-admin', // Aponta para a nova view
        );
    }
}
