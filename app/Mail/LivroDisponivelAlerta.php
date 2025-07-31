<?php

namespace App\Mail;

use App\Models\Livro; // <-- Importar o modelo Livro
use App\Models\User;  // <-- Importar o modelo User
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LivroDisponivelAlerta extends Mailable
{
    use Queueable, SerializesModels;

    // As nossas propriedades públicas para guardar os dados
    public Livro $livro;
    public User $user;

    /**
     * Create a new message instance.
     */
    public function __construct(int $livroId, int $userId) // <-- Agora recebe IDs (números inteiros)
    {
        // Busca os objetos frescos da base de dados usando os IDs
        $this->livro = Livro::findOrFail($livroId);
        $this->user = User::findOrFail($userId);
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
        // A view a ser usada para o email
        return new Content(
            markdown: 'emails.livros.disponivel-alerta',
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