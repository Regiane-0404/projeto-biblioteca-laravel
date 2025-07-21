<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Editora; // Adicionar este import
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Mostra a página inicial pública com o catálogo de livros.
     */
    public function index(Request $request)
    {
        $query = Livro::where('ativo', true)->with(['editora', 'autores']);

        // APLICA O NOVO FILTRO DE EDITORA
        if ($request->filled('editora')) {
            $query->where('editora_id', $request->editora);
        }

        // Aplica o filtro de pesquisa, se existir
        if ($request->filled('search')) {
            $termo = $request->search;
            $todosLivros = $query->get();
            $livrosFiltrados = $todosLivros->filter(function ($livro) use ($termo) {
                if (stripos($livro->nome, $termo) !== false) return true;
                foreach ($livro->autores as $autor) {
                    if (stripos($autor->nome, $termo) !== false) return true;
                }
                return false;
            });
            $livros = new \Illuminate\Pagination\LengthAwarePaginator(
                $livrosFiltrados->forPage(\Illuminate\Pagination\Paginator::resolveCurrentPage(), 12),
                $livrosFiltrados->count(),
                12,
                \Illuminate\Pagination\Paginator::resolveCurrentPage(),
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $livros = $query->latest()->paginate(12);
        }

        // BUSCA A LISTA DE EDITORAS PARA O DROPDOWN
        $editoras = Editora::orderBy('nome')->get();

        // ENVIA TODAS AS VARIÁVEIS NECESSÁRIAS PARA A VIEW
        return view('home', compact('livros', 'editoras'));
    }
}
