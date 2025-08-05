<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Editora;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Livro::where('ativo', true)->with(['editora', 'autores']);
        if ($request->filled('editora')) {
            $query->where('editora_id', $request->editora);
        }

        $todosLivros = $query->get();

        // =======================================================
        // == A CORREÇÃO ESTÁ AQUI DENTRO                       ==
        // =======================================================
        $todosLivros->each(function ($livro) {
            $livro->nome_visivel = $livro->nome;

            $valor = preg_replace('/[^\d,\.]/', '', $livro->preco);
            $valor = str_replace(',', '.', $valor);
            $livro->preco_visivel = (float) $valor;

            $livro->autores->each(function ($autor) {
                $autor->nome_visivel = $autor->nome;
            });
        });


        $livrosFiltrados = $todosLivros;
        if ($request->filled('search')) {
            $termo = $request->search;
            $livrosFiltrados = $todosLivros->filter(function ($livro) use ($termo) {
                if (stripos($livro->nome_visivel, $termo) !== false) return true;
                foreach ($livro->autores as $autor) {
                    if (stripos($autor->nome_visivel, $termo) !== false) return true;
                }
                return false;
            });
        }

        $page = Paginator::resolveCurrentPage('page');
        $perPage = 12;
        $livros = new LengthAwarePaginator(
            $livrosFiltrados->forPage($page, $perPage),
            $livrosFiltrados->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $editoras = Editora::orderBy('nome')->get();
        $editoras->each(function ($editora) {
            $editora->nome_visivel = $editora->nome;
        });

        // A variável $reviewsRecentes precisa de ser adicionada aqui também, se quisermos o carrossel
        // Supondo que o modelo Review está importado no topo.
        // use App\Models\Review;
        $reviewsRecentes = \App\Models\Review::where('status', 'aprovado')->with(['user', 'livro'])->latest()->take(3)->get();

        return view('home', compact('livros', 'editoras', 'reviewsRecentes'));
    }
}
