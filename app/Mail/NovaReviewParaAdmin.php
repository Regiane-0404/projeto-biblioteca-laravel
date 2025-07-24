<?php

namespace App\Mail;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// GARANTA QUE O NOME DA CLASSE É EXATAMENTE ESTE
class NovaReviewParaAdmin extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Review $review;

    /**
     * Create a new message instance.
     */
    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nova Avaliação Recebida para Moderação',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.nova-review',
        );
    }
}
