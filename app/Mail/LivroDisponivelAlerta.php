<?php

namespace App\Mail;

use App\Models\Livro;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LivroDisponivelAlerta extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Livro $livro;
    public User $user;

    /**
     * Create a new message instance.
     */
    public function __construct(Livro $livro, User $user)
    {
        $this->livro = $livro;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Boas notícias! O livro que você queria está disponível',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.livros.disponivel-alerta', // Aponta para a view que vamos criar
        );
    }
}
