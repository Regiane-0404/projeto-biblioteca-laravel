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
        $todosLivros->each(function ($livro) {
            $livro->nome_visivel = $livro->nome;
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

        return view('home', compact('livros', 'editoras'));
    }
}
