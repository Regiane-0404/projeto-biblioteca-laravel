<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TesteDeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Email de Teste da Biblioteca Laravel',
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: '<h1>Olá!</h1><p>Se você recebeu este email, a sua configuração SMTP está a funcionar perfeitamente!</p>',
        );
    }
}