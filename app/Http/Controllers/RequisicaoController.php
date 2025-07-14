<?php

namespace App\Http\Controllers;

use App\Models\Requisicao;
use App\Models\Livro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class RequisicaoController extends Controller
{
    public function index()
    {
        if (Auth::user()->role === 'admin') {
            $requisicoes = Requisicao::with(['user', 'livro'])->latest()->paginate(10);
        } else {
            $requisicoes = Auth::user()->requisicoes()->with('livro')->latest()->paginate(10);
        }

        return view('requisicoes.index', compact('requisicoes'));
    }

    public function create(Request $request)
    {
        $search = $request->get('search', '');

        // Buscar todos os livros disponíveis
        $query = Livro::whereDoesntHave('requisicaoAtiva')
            ->with(['editora', 'autores']);

        // Se tem pesquisa, filtrar DEPOIS de carregar (dados criptografados)
        if ($search) {
            $todosLivros = $query->get();

            $livrosFiltrados = $todosLivros->filter(function ($livro) use ($search) {
                $searchLower = strtolower($search);

                // Pesquisar no nome do livro
                if (str_contains(strtolower($livro->nome), $searchLower)) {
                    return true;
                }

                // Pesquisar na editora
                if (str_contains(strtolower($livro->editora->nome), $searchLower)) {
                    return true;
                }

                // Pesquisar nos autores (CORRIGIDO para criptografia e acentuação)
                foreach ($livro->autores as $autor) {
                    $nomeAutor = $autor->getAttribute('nome');
                    $nomeNormalizado = $this->removeAcentos($nomeAutor);
                    $searchNormalizado = $this->removeAcentos($search);

                    if (str_contains($nomeNormalizado, $searchNormalizado)) {
                        return true;
                    }
                }

                return false;
            });

            // Ordenar por nome
            $livrosFiltrados = $livrosFiltrados->sortBy('nome');

            // Criar paginação manual
            $page = $request->get('page', 1);
            $perPage = 12;
            $offset = ($page - 1) * $perPage;

            $livrosDisponiveis = new LengthAwarePaginator(
                $livrosFiltrados->slice($offset, $perPage),
                $livrosFiltrados->count(),
                $perPage,
                $page,
                [
                    'path' => $request->url(),
                    'query' => $request->query()
                ]
            );
        } else {
            // Sem pesquisa, usar paginação normal e ordenar por nome
            $todosLivros = $query->get()->sortBy('nome');

            $page = $request->get('page', 1);
            $perPage = 12;
            $offset = ($page - 1) * $perPage;

            $livrosDisponiveis = new LengthAwarePaginator(
                $todosLivros->slice($offset, $perPage),
                $todosLivros->count(),
                $perPage,
                $page,
                [
                    'path' => $request->url(),
                    'query' => $request->query()
                ]
            );
        }

        return view('requisicoes.create', compact('livrosDisponiveis', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'livros_ids' => 'required|array|min:1|max:3',
            'livros_ids.*' => 'exists:livros,id',
        ]);

        // Verificar limite ATUAL do usuário (não incluir os novos ainda)
        $requisicaesAtivas = Auth::user()->requisicaesAtivas()->count();
        $totalNovos = count($request->livros_ids);

        if ($requisicaesAtivas + $totalNovos > 3) {
            return back()->with('error', 'Você já possui ' . $requisicaesAtivas . ' livro(s) ativo(s). Não pode adicionar ' . $totalNovos . ' novo(s). Limite máximo: 3 livros.');
        }

        $livrosCriados = 0;

        // Criar requisições para cada livro selecionado
        foreach ($request->livros_ids as $livro_id) {
            $livro = Livro::findOrFail($livro_id);

            // Verificar se livro ainda está disponível
            if (!$livro->isDisponivel()) {
                continue;
            }

            Requisicao::create([
                'user_id' => Auth::id(),
                'livro_id' => $livro->id,
                'data_inicio' => Carbon::now(),
                'data_fim_prevista' => Carbon::now()->addDays(5),
            ]);

            $livrosCriados++;
        }

        if ($livrosCriados > 0) {
            return redirect()->route('requisicoes.index')->with('success', $livrosCriados . ' requisição(ões) criada(s) com sucesso!');
        } else {
            return back()->with('error', 'Nenhuma requisição foi criada. Livros podem estar indisponíveis.');
        }
    }


    // Função para remover acentos
    private function removeAcentos($string)
    {
        $acentos = [
            'á',
            'à',
            'ã',
            'â',
            'ä',
            'é',
            'è',
            'ê',
            'ë',
            'í',
            'ì',
            'î',
            'ï',
            'ó',
            'ò',
            'õ',
            'ô',
            'ö',
            'ú',
            'ù',
            'û',
            'ü',
            'ç',
            'ñ'
        ];
        $sem_acentos = [
            'a',
            'a',
            'a',
            'a',
            'a',
            'e',
            'e',
            'e',
            'e',
            'i',
            'i',
            'i',
            'i',
            'o',
            'o',
            'o',
            'o',
            'o',
            'u',
            'u',
            'u',
            'u',
            'c',
            'n'
        ];
        return str_replace($acentos, $sem_acentos, strtolower($string));
    }

    public function cancelar(Requisicao $requisicao)
    {
        if ($requisicao->status === 'solicitado') {
            // Deletar a requisição em vez de cancelar
            $requisicao->delete();
            return back()->with('success', 'Requisição cancelada e removida com sucesso.');
        }

        return back()->with('error', 'A requisição não pode ser cancelada.');
    }


    /**
     * Permite que um cidadão cancele a sua própria requisição,
     * se ela ainda não foi aprovada.
     */
    public function cancelarCidadao(Requisicao $requisicao)
    {
        $user = auth()->user();

        if (($user->id === $requisicao->user_id && $requisicao->status === 'solicitado') || $user->role === 'admin') {
            // Se a condição for cumprida, deletamos a requisição
            $requisicao->delete();
            return back()->with('success', 'Requisição cancelada com sucesso.');
        }

        // Se nenhuma das condições for cumprida, negamos o acesso.
        return back()->with('error', 'Você não tem permissão para cancelar esta requisição.');
    }


    // ADICIONE ESTES DOIS MÉTODOS AO SEU REQUISICAOCONTROLLER

    public function aprovar(Requisicao $requisicao)
    {
        // Apenas Admins podem aprovar
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Ação não autorizada.');
        }

        $requisicao->update(['status' => 'aprovado']);

        return back()->with('success', 'Requisição aprovada com sucesso!');
    }

    public function entregar(Request $request, Requisicao $requisicao)
    {
        // Apenas Admins podem confirmar a entrega
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Ação não autorizada.');
        }

        // Validamos a data que vem do formulário
        $validated = $request->validate([
            'data_fim_real' => 'required|date',
            'observacoes' => 'nullable|string|max:500',
        ]);

        // Convertemos a data para um objeto Carbon para fazer cálculos
        $dataFimReal = Carbon::parse($validated['data_fim_real']);
        $dataFimPrevista = $requisicao->data_fim_prevista;

        // Calculamos os dias de atraso
        $diasAtraso = $dataFimReal->isAfter($dataFimPrevista)
            ? $dataFimReal->diffInDays($dataFimPrevista)
            : 0;

        // Atualizamos a requisição com os novos dados
        $requisicao->update([
            'status' => 'devolvido', // Mudamos o status para 'devolvido'
            'data_fim_real' => $dataFimReal,
            'observacoes' => $validated['observacoes'],
            'dias_atraso' => $diasAtraso,
        ]);

        return back()->with('success', 'Devolução registrada com sucesso!');
    }
}
