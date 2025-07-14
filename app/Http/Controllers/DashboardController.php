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
    // Este é o método que o ficheiro de rotas está à procura!
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Se for admin, carrega o dashboard de admin (que já existe)
            return $this->adminDashboard();
        } else {
            // Se for cidadão, carrega o novo dashboard de cidadão
            return $this->cidadaoDashboard();
        }
    }

    /**
     * Lógica para o dashboard do Admin.
     */
    private function adminDashboard()
    {
        // O dashboard do Admin continua a usar a vista 'dashboard' que já tínhamos.
        return view('dashboard');
    }

    /**
     * Lógica para o dashboard do Cidadão.
     */
    private function cidadaoDashboard()
    {
        $user = Auth::user();

        // Estatísticas para o cidadão
       $stats = [
        'total_requisicoes' => $user->requisicoes()->count(), // <-- NOME CORRIGIDO
        'requisicoes_ativas' => $user->requisicaesAtivas()->count(),
        'livros_disponiveis' => Livro::whereDoesntHave('requisicaoAtiva')->count(),
        ];

        // Pega as 5 requisições mais recentes para mostrar na lista
        $requisicoes_recentes = $user->requisicoes()->with('livro')->latest()->take(5)->get();

        // Aqui está a chave: ele vai procurar um novo ficheiro de vista
        // que ainda vamos criar.
        return view('dashboard-cidadao', compact('stats', 'requisicoes_recentes'));
    }
}
