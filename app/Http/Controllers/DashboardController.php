<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use App\Models\Requisicao;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return $this->adminDashboard();
        } else {
            return $this->cidadaoDashboard();
        }
    }

    private function adminDashboard()
    {
        // 1. Primeiro, criamos o array com as estatísticas.
        $stats = [
            'livros_ativos' => Livro::where('ativo', true)->count(),
            'autores' => Autor::count(),
            'editoras' => Editora::count(),
        ];

        // 2. DEPOIS, e em separado, buscamos a lista de livros recentes.
        $livros_recentes = Livro::latest()->take(5)->get();

        // 3. Finalmente, enviamos as DUAS variáveis (stats e livros_recentes) para a view.
        return view('dashboard', compact('stats', 'livros_recentes'));
    }

    private function cidadaoDashboard()
    {
        $user = Auth::user();

        $stats = [
            'total_requisicoes' => $user->requisicoes()->count(),
            // =======================================================
            // CORREÇÃO 1: NOME DO MÉTODO CORRIGIDO
            // =======================================================
            'requisicoes_ativas' => $user->requisicoesAtivas()->count(), // Era 'requisicaesAtivas'
            'livros_disponiveis' => Livro::whereDoesntHave('requisicaoAtiva')->count(),
        ];

        $requisicoes_recentes = $user->requisicoes()->with('livro')->latest()->take(5)->get();

        // =======================================================
        // CORREÇÃO 2: ADICIONAR LÓGICA DE DESENCRIPTAÇÃO
        // =======================================================
        $requisicoes_recentes->each(function ($requisicao) {
            if ($requisicao->livro) {
                // Força a desencriptação para que `->nome` funcione na view
                $requisicao->livro->nome = $requisicao->livro->nome;
            }
        });

        // Apontamos para a sua excelente view 'dashboard-cidadao'
        return view('dashboard-cidadao', compact('stats', 'requisicoes_recentes'));
    }
}
