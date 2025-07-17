<?php

namespace App\Mail;

use App\Models\Requisicao;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; // 1. ADICIONE ESTE IMPORT
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// 2. ADICIONE "implements ShouldQueue" AQUI
class RequisicaoCriada extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * A instância da requisição que contém todos os dados.
     */
    public Requisicao $requisicao;

    /**
     * Cria uma nova instância da mensagem.
     * Nós "injetamos" o objeto da requisição aqui.
     */
    public function __construct(Requisicao $requisicao)
    {
        $this->requisicao = $requisicao;
    }

    /**
     * Define o "envelope" do email: o assunto, o remetente, etc.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address'),
            subject: 'Confirmação da sua Requisição #' . $this->requisicao->numero_sequencial,
        );
    }

    /**
     * Define o conteúdo do email, apontando para um ficheiro de view.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.requisicoes.criada', 
        );
    }
}