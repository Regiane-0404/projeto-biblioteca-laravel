<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

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

    /**
     * Mostra o formulário para pesquisar e importar livros da Google API.
     */
    public function mostrarFormularioImportacao()
    {
        // Por enquanto, esta função apenas retorna a view.
        // Mais tarde, ela também vai receber os resultados da pesquisa da API.
        return view('livros.importar');
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
        // A linha abaixo carrega as relações de forma um pouco diferente,
        // o que pode ser mais robusto em certos cenários.
        // Primeiro carrega o básico, depois as requisições.
        $livro->load('editora', 'autores');

        $livro->load(['requisicoes' => function ($query) {
            $query->with('user')->orderBy('created_at', 'desc');
        }]);

        // Agora, passamos para a view.
        return view('livros.show', compact('livro'));
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


    /* Pesquisa livros na Google Books API e mostra os resultados.
     */
    public function pesquisarNaGoogleAPI(Request $request)
    {
        // 1. Validamos para garantir que um termo de pesquisa foi enviado.
        $request->validate(['termo_pesquisa' => 'required|string|min:3']);

        $termo = $request->termo_pesquisa;
        $apiKey = env('GOOGLE_BOOKS_API_KEY'); // Precisaremos de criar esta chave

        // Se a chave da API não estiver configurada, mostramos um erro amigável.
        if (!$apiKey) {
            return back()->with('error', 'A chave da Google Books API não está configurada no sistema.');
        }

        // 2. Fazemos a chamada à API usando o HTTP Client do Laravel.
        $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
            'q' => $termo,
            'key' => $apiKey,
            'maxResults' => 20, // Limitamos a 20 resultados para não sobrecarregar
            'printType' => 'books',
        ]);

        // 3. Verificamos se a resposta foi bem-sucedida.
        if ($response->failed()) {
            // Vamos fazer um "dump and die" da resposta para ver o erro exato
            //dd($response->body(), $response->status());

            return back()->with('error', 'Falha ao comunicar com a Google Books API.');
        }

        // 4. Processamos os resultados. A API pode não retornar nada.
        $items = $response->json()['items'] ?? [];

        // 5. Transformamos os dados brutos da API num formato mais limpo para a nossa view.
        $resultados = collect($items)->map(function ($item) {
            $info = $item['volumeInfo'];
            return (object) [
                'google_id' => $item['id'],
                'titulo' => $info['title'] ?? 'Título não disponível',
                'autores' => isset($info['authors']) ? implode(', ', $info['authors']) : 'Autor não informado',
                'editora' => $info['publisher'] ?? 'Editora não informada',
                'isbn' => $info['industryIdentifiers'][0]['identifier'] ?? null, // Pega o primeiro ISBN
                'capa_url' => $info['imageLinks']['thumbnail'] ?? null,
            ];
        });

        // 6. Retornamos para a mesma view de importação, mas agora passando os resultados.
        return view('livros.importar', [
            'resultados' => $resultados,
            'termo_pesquisa' => $termo, // Para manter o termo na caixa de pesquisa
        ]);
    }


    /**
     * Guarda um livro selecionado da API na base de dados local.
     */
    public function guardarLivroImportado(Request $request)
    {
        // 1. Validação simples para garantir que temos os dados mínimos
        $dados = $request->validate([
            'titulo' => 'required|string',
            'isbn' => 'required|string',
            'autores' => 'nullable|string',
            'editora' => 'nullable|string',
            'capa_url' => 'nullable|url',
        ]);

        // 2. Normaliza o ISBN para a verificação (remove hífens e espaços)
        $isbnNormalizado = preg_replace('/[\s-]+/', '', $dados['isbn']);

        // 3. Verifica se um livro com este ISBN já existe
        if (Livro::where('isbn', $isbnNormalizado)->exists()) {
            return back()->with('error', "O livro '{$dados['titulo']}' (ISBN: {$dados['isbn']}) já existe na sua biblioteca.");
        }

        // 4. Lógica de "Procurar ou Criar" para a Editora
        // Se a editora não for nula, procura por ela. Se não encontrar, cria uma nova.
        $editoraModel = null;
        if (!empty($dados['editora'])) {
            $editoraModel = Editora::firstOrCreate(['nome' => $dados['editora']]);
        }

        // 5. Lógica de "Procurar ou Criar" para os Autores
        $autoresIds = [];
        if (!empty($dados['autores'])) {
            // A API pode devolver vários autores separados por vírgula
            $listaAutores = explode(',', $dados['autores']);
            foreach ($listaAutores as $nomeAutor) {
                $nomeAutor = trim($nomeAutor); // Limpa espaços extra
                if (!empty($nomeAutor)) {
                    $autorModel = Autor::firstOrCreate(['nome' => $nomeAutor]);
                    $autoresIds[] = $autorModel->id;
                }
            }
        }

        // 6. Finalmente, cria o Livro na nossa base de dados
        $novoLivro = Livro::create([
            'nome' => $dados['titulo'],
            'isbn' => $isbnNormalizado,
            'editora_id' => $editoraModel ? $editoraModel->id : null,
            'bibliografia' => 'Importado via Google Books API.', // Valor padrão
            'preco' => 0.00, // Valor padrão, pode ser editado depois
            'imagem_capa' => $dados['capa_url'], // Guardamos o URL da capa
            'ativo' => true,
        ]);

        // 7. Associa os autores ao novo livro
        if (!empty($autoresIds)) {
            $novoLivro->autores()->sync($autoresIds);
        }

        // 8. Retorna com uma mensagem de sucesso
        return redirect()->route('livros.importar.form')->with('success', "O livro '{$novoLivro->nome}' foi importado com sucesso!");
    }
}
