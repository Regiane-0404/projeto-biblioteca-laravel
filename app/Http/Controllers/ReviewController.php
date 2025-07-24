<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Mostra a lista de reviews pendentes para moderação do Admin.
     */
    public function index()
    {
        // 1. Buscamos todas as reviews que têm o status 'pendente'.
        //    Carregamos as relações 'user' e 'livro' para mostrar os seus nomes.
        $reviewsPendentes = Review::where('status', 'pendente')
            ->with(['user', 'livro'])
            ->latest() // Mostra as mais recentes primeiro
            ->paginate(10);

        // 2. Forçamos a desencriptação dos nomes dos livros.
        $reviewsPendentes->getCollection()->each(function ($review) {
            if ($review->livro) {
                $review->livro->nome_visivel = $review->livro->nome;
            }
        });

        // 3. Retornamos a view do painel de moderação.
        return view('admin.reviews.index', compact('reviewsPendentes'));
    }

    /**
     * Aprova uma review.
     */
    public function aprovar(Review $review)
    {
        $review->update(['status' => 'aprovado']);

        // Aqui enviaremos o email para o cidadão
        // Mail::to($review->user->email)->send(new SuaReviewAprovada($review));

        return back()->with('success', 'A avaliação foi aprovada e está agora pública.');
    }

    /**
     * Mostra o formulário para o Admin justificar a recusa.
     */
    public function mostrarFormularioRecusa(Review $review)
    {
        return view('admin.reviews.recusar', compact('review'));
    }

    /**
     * Regista a recusa de uma review.
     */
    public function recusar(Request $request, Review $review)
    {
        $validated = $request->validate([
            'justificacao_recusa' => 'required|string|min:10|max:500',
        ]);

        $review->update([
            'status' => 'recusado',
            'justificacao_recusa' => $validated['justificacao_recusa'],
        ]);

        // Aqui enviaremos o email para o cidadão com a justificação
        // Mail::to($review->user->email)->send(new SuaReviewRecusada($review));

        return redirect()->route('admin.reviews.index')
            ->with('success', 'A avaliação foi recusada com sucesso.');
    }
}
