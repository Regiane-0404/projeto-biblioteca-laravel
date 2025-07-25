<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReviewAprovada;
use App\Mail\ReviewRecusada;

class ReviewController extends Controller
{
    /**
     * Mostra a lista de reviews pendentes.
     */
    public function index()
    {
        $reviewsPendentes = Review::where('status', 'pendente')
            ->with(['user', 'livro'])
            ->latest()
            ->paginate(10);

        $reviewsPendentes->getCollection()->each(function ($review) {
            if ($review->livro) {
                $review->livro->nome_visivel = $review->livro->nome;
            }
        });

        return view('admin.reviews.index', compact('reviewsPendentes'));
    }

    /**
     * Aprova uma review.
     */
    public function aprovar(Review $review)
    {
        $review->update(['status' => 'aprovado']);

        // =============================================
        //  CHAMADA CORRIGIDA (SEM O \App\Mail\)
        // =============================================
        Mail::to($review->user->email)->queue(new ReviewAprovada($review));

        return back()->with('success', 'A avaliação foi aprovada e está agora pública.');
    }

    /**
     * Mostra o formulário para a recusa.
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

        // =============================================
        //  CHAMADA CORRIGIDA (SEM O \App\Mail\)
        // =============================================
        Mail::to($review->user->email)->queue(new ReviewRecusada($review));

        return redirect()->route('admin.reviews.index')
            ->with('success', 'A avaliação foi recusada com sucesso.');
    }
}
