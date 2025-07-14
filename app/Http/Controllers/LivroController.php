<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;

class LivroController extends Controller
{
    public function index(Request $request)
    {
        // 1. Começar com query builder
        $query = Livro::with(['editora', 'autores']);

        // 2. Filtro por status (ativo/inativo/todos)
        $status = $request->get('status', 'ativo');
        if ($status === 'ativo') {
            $query->where('ativo', true);
        } elseif ($status === 'inativo') {
            $query->where('ativo', false);
        }
        // Se $status === 'todos', não aplica filtro

        // 3. Se há filtro por editora
        if ($request->has('editora') && $request->editora != '') {
            $query->where('editora_id', $request->editora);
        }

        // 4. Buscar todos os registros
        $allLivros = $query->get();

        // 5. Se há pesquisa, filtrar após descriptografar
        if ($request->has('search') && $request->search != '') {
            $search = strtolower($request->search);

            $filteredLivros = $allLivros->filter(function ($livro) use ($search) {
                // Buscar no nome do livro
                if (stripos($livro->nome, $search) !== false) {
                    return true;
                }

                // Buscar no ISBN
                if (stripos($livro->isbn, $search) !== false) {
                    return true;
                }

                // Buscar na bibliografia
                if ($livro->bibliografia && stripos($livro->bibliografia, $search) !== false) {
                    return true;
                }

                // Buscar nos autores
                foreach ($livro->autores as $autor) {
                    if (stripos($autor->nome, $search) !== false) {
                        return true;
                    }
                }

                // Buscar na editora
                if ($livro->editora && stripos($livro->editora->nome, $search) !== false) {
                    return true;
                }

                return false;
            });
        } else {
            $filteredLivros = $allLivros;
        }

        // 6. Ordenação (CORRIGIDA PARA CAMPOS ENCRIPTADOS)
        $orderBy = $request->get('order_by', 'created_at');
        $orderDirection = $request->get('order_direction', 'desc');

        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        // Ordenar collection
        if ($orderBy == 'nome') {
            $filteredLivros = $orderDirection == 'asc'
                ? $filteredLivros->sortBy('nome')
                : $filteredLivros->sortByDesc('nome');
        } elseif ($orderBy == 'preco') {
            // CORREÇÃO: Ordenação por preço com campos encriptados
            $filteredLivros = $orderDirection == 'asc'
                ? $filteredLivros->sortBy(function ($livro) {
                    return (float) str_replace(',', '.', $livro->preco);
                })
                : $filteredLivros->sortByDesc(function ($livro) {
                    return (float) str_replace(',', '.', $livro->preco);
                });
        } elseif ($orderBy == 'isbn') {
            $filteredLivros = $orderDirection == 'asc'
                ? $filteredLivros->sortBy('isbn')
                : $filteredLivros->sortByDesc('isbn');
        } else {
            $filteredLivros = $orderDirection == 'asc'
                ? $filteredLivros->sortBy('created_at')
                : $filteredLivros->sortByDesc('created_at');
        }

        // 7. Paginação manual
        $perPage = 4;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $offset = ($currentPage - 1) * $perPage;

        $livrosPaginated = $filteredLivros->slice($offset, $perPage);

        // 8. Criar paginação
        $livros = new LengthAwarePaginator(
            $livrosPaginated->values(),
            $filteredLivros->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        // 9. Buscar editoras para o filtro
        $editoras = Editora::all();

        // 10. Retornar view com dados
        return view('livros.index', compact('livros', 'editoras', 'status'));
    }



    public function create()
    {
        $editoras = Editora::all();
        $autores = Autor::all();
        return view('livros.create', compact('editoras', 'autores'));
    }

    public function store(Request $request)
    {
        // 1. Validação dos dados
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'isbn' => 'required|string|unique:livros,isbn|max:50',
            'editora_id' => 'required|exists:editoras,id',
            'preco' => 'required|numeric|min:0',
            'autores' => 'required|array|min:1',
            'autores.*' => 'exists:autors,id',
            'bibliografia' => 'nullable|string|max:1000',
            'imagem_capa' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'nome.required' => 'O nome do livro é obrigatório',
            'isbn.required' => 'O ISBN é obrigatório',
            'isbn.unique' => 'Este ISBN já existe no sistema',
            'editora_id.required' => 'Selecione uma editora',
            'editora_id.exists' => 'Editora inválida',
            'preco.required' => 'O preço é obrigatório',
            'preco.numeric' => 'O preço deve ser um número',
            'preco.min' => 'O preço não pode ser negativo',
            'autores.required' => 'Selecione pelo menos um autor',
            'autores.min' => 'Selecione pelo menos um autor',
            'imagem_capa.image' => 'O arquivo deve ser uma imagem',
            'imagem_capa.mimes' => 'Formatos aceitos: JPEG, PNG, JPG, GIF',
            'imagem_capa.max' => 'A imagem não pode ser maior que 2MB'
        ]);

        // 2. Upload da imagem se existir
        $imagemPath = null;
        if ($request->hasFile('imagem_capa')) {
            $imagemPath = $request->file('imagem_capa')->store('capas', 'public');
        }

        // 3. Criar o livro
        $livro = Livro::create([
            'nome' => $validated['nome'],
            'isbn' => $validated['isbn'],
            'editora_id' => $validated['editora_id'],
            'preco' => $validated['preco'],
            'bibliografia' => $validated['bibliografia'],
            'imagem_capa' => $imagemPath,
            'ativo' => true, // Novo livro sempre ativo
        ]);

        // 4. Associar autores
        $livro->autores()->attach($validated['autores']);

        // 5. Retornar com sucesso
        return redirect()->route('livros.index')
            ->with('success', 'Livro criado com sucesso!');
    }

    public function show(Livro $livro)
    {
        $livro->load([
            'editora',
            'autores',
            'requisicoes' => function ($query) {
                $query->with('user')->orderBy('created_at', 'desc');
            }
        ]);

       
    }

    public function edit(Livro $livro)
    {
        $editoras = Editora::all();
        $autores = Autor::all();
        return view('livros.edit', compact('livro', 'editoras', 'autores'));
    }

    public function update(Request $request, Livro $livro)
    {
        // 1. Validação dos dados
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'isbn' => 'required|string|max:50|unique:livros,isbn,' . $livro->id,
            'editora_id' => 'required|exists:editoras,id',
            'preco' => 'required|numeric|min:0',
            'autores' => 'required|array|min:1',
            'autores.*' => 'exists:autors,id',
            'bibliografia' => 'nullable|string|max:1000',
            'imagem_capa' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'nome.required' => 'O nome do livro é obrigatório',
            'isbn.required' => 'O ISBN é obrigatório',
            'isbn.unique' => 'Este ISBN já existe no sistema',
            'editora_id.required' => 'Selecione uma editora',
            'editora_id.exists' => 'Editora inválida',
            'preco.required' => 'O preço é obrigatório',
            'preco.numeric' => 'O preço deve ser um número',
            'preco.min' => 'O preço não pode ser negativo',
            'autores.required' => 'Selecione pelo menos um autor',
            'autores.min' => 'Selecione pelo menos um autor',
            'imagem_capa.image' => 'O arquivo deve ser uma imagem',
            'imagem_capa.mimes' => 'Formatos aceitos: JPEG, PNG, JPG, GIF',
            'imagem_capa.max' => 'A imagem não pode ser maior que 2MB'
        ]);

        // 2. Upload da nova imagem se existir
        $imagemPath = $livro->imagem_capa; // Manter a imagem atual
        if ($request->hasFile('imagem_capa')) {
            // Deletar imagem antiga se existir
            if ($livro->imagem_capa && Storage::disk('public')->exists($livro->imagem_capa)) {
                Storage::disk('public')->delete($livro->imagem_capa);
            }

            // Salvar nova imagem
            $imagemPath = $request->file('imagem_capa')->store('capas', 'public');
        }

        // 3. Atualizar o livro
        $livro->update([
            'nome' => $validated['nome'],
            'isbn' => $validated['isbn'],
            'editora_id' => $validated['editora_id'],
            'preco' => $validated['preco'],
            'bibliografia' => $validated['bibliografia'],
            'imagem_capa' => $imagemPath,
        ]);

        // 4. Atualizar autores (remover os antigos e adicionar os novos)
        $livro->autores()->sync($validated['autores']);

        // 5. Retornar com sucesso
        return redirect()->route('livros.index')
            ->with('success', 'Livro atualizado com sucesso!');
    }

    public function destroy(Livro $livro)
    {
        $nome = $livro->nome;

        // VALIDAÇÃO DE DEPENDÊNCIAS MELHORADA
        $dependencias = [];

        // Verificar autores
        if ($livro->autores->count() > 0) {
            $autores = $livro->autores->pluck('nome')->toArray();
            $dependencias[] = "Autores: " . implode(', ', $autores);
        }

        // Verificar editora (opcional - só se quiser também)
        if ($livro->editora) {
            $dependencias[] = "Editora: " . $livro->editora->nome;
        }

        // SE TEM DEPENDÊNCIAS, NÃO PERMITIR EXCLUSÃO
        if (!empty($dependencias)) {
            return redirect()->route('livros.index')
                ->with('error', "❌ Não foi possível excluir o livro \"{$nome}\". Este livro possui dados associados. ");
        }

        // SE NÃO TEM DEPENDÊNCIAS, PODE EXCLUIR
        try {
            // Deletar imagem se existir
            if ($livro->imagem_capa && Storage::disk('public')->exists($livro->imagem_capa)) {
                Storage::disk('public')->delete($livro->imagem_capa);
            }

            // Deletar registro
            $livro->delete();

            return redirect()->route('livros.index')
                ->with('success', "✅ Livro \"{$nome}\" foi excluído permanentemente do sistema!");
        } catch (\Exception $e) {
            return redirect()->route('livros.index')
                ->with('error', "❌ Erro ao excluir o livro. Tente novamente ou contacte o suporte.");
        }
    }

    public function inativar(Livro $livro)
    {
        $livro->update(['ativo' => false]);

        return redirect()->route('livros.index')
            ->with('warning', 'Livro inativado com sucesso! Ele não aparecerá mais na listagem principal, mas permanece no sistema para histórico.');
    }

    public function ativar(Livro $livro)
    {
        $livro->update(['ativo' => true]);

        return redirect()->route('livros.index')
            ->with('success', 'Livro ativado com sucesso!');
    }

    public function exportar(Request $request)
    {
        $query = Livro::with(['editora', 'autores']);

        $status = $request->get('status', 'ativo');
        if ($status === 'ativo') {
            $query->where('ativo', true);
        } elseif ($status === 'inativo') {
            $query->where('ativo', false);
        }

        if ($request->has('editora') && $request->editora != '') {
            $query->where('editora_id', $request->editora);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('nome', 'like', "%{$search}%")
                ->orWhere('isbn', 'like', "%{$search}%");
        }

        $livros = $query->orderBy('nome')->get();
        $filename = 'livros_relatorio_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($livros) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['ID', 'Nome do Livro', 'ISBN', 'Editora', 'Autores', 'Bibliografia', 'Preço (€)', 'Status', 'Data de Cadastro', 'Última Atualização'], ';');

            foreach ($livros as $livro) {
                fputcsv($file, [
                    $livro->id,
                    $livro->nome,
                    $livro->isbn,
                    $livro->editora ? $livro->editora->nome : 'Sem editora',
                    $livro->autores->count() > 0 ? $livro->autores->pluck('nome')->join(', ') : 'Sem autores',
                    $livro->bibliografia ?: 'Sem descrição',
                    number_format($livro->preco, 2, ',', '.'),
                    $livro->ativo ? 'Ativo' : 'Inativo',
                    $livro->created_at->format('d/m/Y H:i'),
                    $livro->updated_at->format('d/m/Y H:i'),
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
