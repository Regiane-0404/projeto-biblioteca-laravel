<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection; // Importar a classe Collection

class AbandonedCartReminder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * As nossas variáveis públicas estarão disponíveis na view.
     */
    public string $userName;
    public Collection $cartItems;

    /**
     * Create a new message instance.
     */
    public function __construct(string $userName, Collection $cartItems)
    {
        $this->userName = $userName;
        $this->cartItems = $cartItems;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Esqueceu-se de algo no seu carrinho?',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Apontamos para a nossa nova view.
        return new Content(
            view: 'emails.cart.abandoned_reminder',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
