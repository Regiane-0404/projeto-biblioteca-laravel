<?php

namespace App\Http\Controllers;

use App\Models\Requisicao;
use App\Models\Livro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;
use App\Mail\RequisicaoCriada;

class RequisicaoController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = ($user->role === 'admin')
            ? Requisicao::query()
            : $user->requisicoes();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('data_de')) {
            $query->whereDate('created_at', '>=', $request->data_de);
        }

        if ($request->filled('data_ate')) {
            $query->whereDate('created_at', '<=', $request->data_ate);
        }

        if (!$request->filled('data_de') && !$request->filled('data_ate')) {
            $query->whereDate('created_at', today());
        }

        $requisicoes = $query->with(['user', 'livro'])->latest()->paginate(10)->withQueryString();

        $requisicoes->getCollection()->each(function ($requisicao) {
            if ($requisicao->livro) {
                $requisicao->livro->nome_visivel = $requisicao->livro->nome;
            }
        });

        return view('requisicoes.index', [
            'requisicoes' => $requisicoes,
            'filtro_status' => $request->status,
            'filtro_data_de' => $request->data_de,
            'filtro_data_ate' => $request->data_ate,
        ]);
    }

    public function create(Request $request)
    {
        $search = $request->get('search', '');

        $query = Livro::whereDoesntHave('requisicaoAtiva')
            ->with(['editora', 'autores']);

        if ($search) {
            $todosLivros = $query->get();

            $livrosFiltrados = $todosLivros->filter(function ($livro) use ($search) {
                $searchLower = strtolower($search);

                if (str_contains(strtolower($livro->nome), $searchLower)) {
                    return true;
                }

                if (str_contains(strtolower($livro->editora->nome), $searchLower)) {
                    return true;
                }

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

            $livrosFiltrados = $livrosFiltrados->sortBy('nome');

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

    /*public function store(Request $request)
    {
        $request->validate([
            'livros_ids' => 'required|array|min:1|max:3',
            'livros_ids.*' => 'exists:livros,id',
        ]);

        $requisicaesAtivas = Auth::user()->requisicaesAtivas()->count();
        $totalNovos = count($request->livros_ids);

        if ($requisicaesAtivas + $totalNovos > 3) {
            return back()->with('error', 'Você já possui ' . $requisicaesAtivas . ' livro(s) ativo(s). Não pode adicionar ' . $totalNovos . ' novo(s). Limite máximo: 3 livros.');
        }

        $livrosCriados = 0;

        foreach ($request->livros_ids as $livro_id) {
            $livro = Livro::findOrFail($livro_id);
            if (!$livro->isDisponivel()) continue;

            // Criamos a requisição e guardamo-la numa variável
            $novaRequisicao = Requisicao::create([
                'user_id' => Auth::id(),
                'livro_id' => $livro->id,
                'data_inicio' => now(),
                'data_fim_prevista' => now()->addDays(5),
            ]);

            // Aqui seria o local para enviar o e-mail
            // Mail::to(Auth::user()->email)->send(new RequisicaoCriadaMail($novaRequisicao));

            $livrosCriados++;
        }

        if ($livrosCriados > 0) {
            return redirect()->route('requisicoes.index')->with('success', $livrosCriados . ' requisição(ões) criada(s) com sucesso!');
        } else {
            return back()->with('error', 'Nenhuma requisição foi criada. Livros podem estar indisponíveis.');
        }
    }*/


    public function store(Request $request)
{
    // ... (toda a sua lógica de validação e verificação de limite) ...

    $livrosCriados = 0;
    foreach ($request->livros_ids as $livro_id) {
        $livro = Livro::find($livro_id);
        if ($livro && $livro->isDisponivel()) {
            
            // 1. A requisição é criada com sucesso.
            $novaRequisicao = Requisicao::create([
                'user_id' => Auth::id(),
                'livro_id' => $livro->id,
                'data_inicio' => now(),
                'data_fim_prevista' => now()->addDays(5)
            ]);
            
            // 2. A LINHA QUE FALTA: Disparamos o email, passando os dados da requisição.
            Mail::to($novaRequisicao->user->email)->send(new RequisicaoCriada($novaRequisicao));

            $livrosCriados++;
        }
    }

    // ... (o resto do seu código de retorno) ...
}





    private function removeAcentos($string)
    {
        $acentos = [
            'á','à','ã','â','ä',
            'é','è','ê','ë',
            'í','ì','î','ï',
            'ó','ò','õ','ô','ö',
            'ú','ù','û','ü',
            'ç','ñ'
        ];
        $sem_acentos = [
            'a','a','a','a','a',
            'e','e','e','e',
            'i','i','i','i',
            'o','o','o','o','o',
            'u','u','u','u',
            'c','n'
        ];
        return str_replace($acentos, $sem_acentos, strtolower($string));
    }

    public function cancelar(Requisicao $requisicao)
    {
        if ($requisicao->status === 'solicitado') {
            $requisicao->delete();
            return back()->with('success', 'Requisição cancelada e removida com sucesso.');
        }

        return back()->with('error', 'A requisição não pode ser cancelada.');
    }

    public function cancelarCidadao(Requisicao $requisicao)
    {
        $user = auth()->user();

        if (($user->id === $requisicao->user_id && $requisicao->status === 'solicitado') || $user->role === 'admin') {
            $requisicao->delete();
            return back()->with('success', 'Requisição cancelada com sucesso.');
        }

        return back()->with('error', 'Você não tem permissão para cancelar esta requisição.');
    }

    public function aprovar(Requisicao $requisicao)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Ação não autorizada.');
        }

        $requisicao->update(['status' => 'aprovado']);

        return back()->with('success', 'Requisição aprovada com sucesso!');
    }

    public function entregar(Request $request, Requisicao $requisicao)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Ação não autorizada.');
        }

        $validated = $request->validate([
            'data_fim_real' => 'required|date',
            'observacoes' => 'nullable|string|max:500',
        ]);

        $dataFimReal = Carbon::parse($validated['data_fim_real']);
        $dataFimPrevista = $requisicao->data_fim_prevista;

        $diasAtraso = $dataFimReal->isAfter($dataFimPrevista)
            ? $dataFimReal->diffInDays($dataFimPrevista)
            : 0;

        $requisicao->update([
            'status' => 'devolvido',
            'data_fim_real' => $dataFimReal,
            'observacoes' => $validated['observacoes'],
            'dias_atraso' => $diasAtraso,
        ]);

        return back()->with('success', 'Devolução registrada com sucesso!');
    }
}
