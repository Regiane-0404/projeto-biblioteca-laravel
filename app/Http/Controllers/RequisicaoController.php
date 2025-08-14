<?php

namespace App\Http\Controllers;

use App\Models\Requisicao;
use App\Models\Livro;
use App\Models\User;
use App\Models\AlertaDisponibilidade;
use App\Mail\LivroDisponivelAlerta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\RequisicaoCriada;
use App\Mail\NovaRequisicaoParaAdmin;
use App\Mail\NovaReviewParaAdmin;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Review;

class RequisicaoController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $viewData = [
            'stats' => [],
            'filtro_status' => $request->status,
            'filtro_data_de' => $request->data_de,
            'filtro_data_ate' => $request->data_ate,
            'active_tab' => 'visao_geral',
            'requisicoes' => collect(),
        ];

        if ($user->role === 'admin') {
            $viewData['stats'] = [
                'ativas' => Requisicao::whereIn('status', ['solicitado', 'aprovado'])->count(),
                'ultimos_30_dias' => Requisicao::where('created_at', '>=', now()->subDays(30))->count(),
                'devolvidos_hoje' => Requisicao::where('status', 'devolvido')->whereDate('data_fim_real', today())->count(),
            ];

            if ($request->has('tab') && $request->tab === 'lista' || $request->hasAny(['data_de', 'data_ate', 'status'])) {
                $viewData['active_tab'] = 'lista';
            }

            if ($viewData['active_tab'] === 'lista') {
                $query = Requisicao::query()->with(['user', 'livro'])->latest();
                if ($request->hasAny(['data_de', 'data_ate', 'status'])) {
                    if ($request->filled('status')) $query->where('status', $request->status);
                    if ($request->filled('data_de')) $query->whereDate('created_at', '>=', $request->data_de);
                    if ($request->filled('data_ate')) $query->whereDate('created_at', '<=', $request->data_ate);
                    $viewData['requisicoes'] = $query->paginate(7)->withQueryString();
                } else {
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    $viewData['requisicoes'] = $query->get();
                }
            }
        } else {
            $viewData['requisicoes'] = $user->requisicoes()->with(['livro'])->latest()->paginate(10)->withQueryString();
        }

        if ($viewData['requisicoes']->isNotEmpty()) {
            $colecao = ($viewData['requisicoes'] instanceof \Illuminate\Pagination\LengthAwarePaginator)
                ? $viewData['requisicoes']->getCollection() : $viewData['requisicoes'];
            $colecao->each(function ($r) {
                if ($r->livro) $r->livro->nome_visivel = $r->livro->nome;
            });
        }
        return view('requisicoes.index', $viewData);
    }

    public function create(Request $request, $livro_id = null)
    {
        $search = $request->get('search', '');
        $query = Livro::where('quantidade', '>', 0)->with(['editora', 'autores']);
        $todosLivros = $query->get();
        $todosLivros->each(function ($livro) {
            $livro->nome_visivel = $livro->nome;
            if ($livro->editora) $livro->editora->nome_visivel = $livro->editora->nome;
            $livro->autores->each(function ($autor) {
                $autor->nome_visivel = $autor->nome;
            });
        });
        $livrosFiltrados = $search ? $todosLivros->filter(function ($livro) use ($search) {
            $termo = $this->removeAcentos($search);
            if (str_contains($this->removeAcentos($livro->nome_visivel), $termo)) return true;
            if ($livro->editora && str_contains($this->removeAcentos($livro->editora->nome_visivel), $termo)) return true;
            foreach ($livro->autores as $autor) {
                if (str_contains($this->removeAcentos($autor->nome_visivel), $termo)) return true;
            }
            return false;
        }) : $todosLivros;
        $livrosOrdenados = $livrosFiltrados->sortBy('nome_visivel');
        if ($livro_id) {
            $livrosOrdenados = $livrosOrdenados->partition(function ($livro) use ($livro_id) {
                return $livro->id == $livro_id;
            })->flatten();
        }
        $page = Paginator::resolveCurrentPage('page');
        $perPage = 12;
        $livrosDisponiveis = new LengthAwarePaginator($livrosOrdenados->forPage($page, $perPage)->values(), $livrosOrdenados->count(), $perPage, $page, ['path' => Paginator::resolveCurrentPath()]);
        $livrosDisponiveis->withQueryString();
        $requisicoesAtivasCount = Auth::user()->requisicoesAtivas()->count();
        return view('requisicoes.create', compact('livrosDisponiveis', 'search', 'livro_id', 'requisicoesAtivasCount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'livros_ids' => 'required|array|min:1|max:3',
            'livros_ids.*' => 'exists:livros,id',
        ], ['livros_ids.required' => 'Você precisa de selecionar pelo menos um livro.']);
        $user = Auth::user();
        if ($user->requisicoesAtivas()->count() + count(array_unique($validated['livros_ids'])) > 3) {
            return back()->with('error', 'Limite de 3 requisições ativas excedido.');
        }
        $livrosCriados = 0;
        foreach (array_unique($validated['livros_ids']) as $livro_id) {
            $livro = Livro::find($livro_id);
            if ($livro && $livro->isDisponivel()) {
                $novaRequisicao = Requisicao::create([
                    'user_id' => $user->id,
                    'livro_id' => $livro_id,
                    'data_inicio' => now(),
                    'data_fim_prevista' => now()->addDays(5),
                ]);
                $livro->decrement('quantidade');
                Mail::to($user->email)->queue(new RequisicaoCriada($novaRequisicao));
                Mail::to('regianecinel@gmail.com')->queue(new NovaRequisicaoParaAdmin($novaRequisicao));
                $livrosCriados++;
            }
        }
        if ($livrosCriados > 0) {
            $mensagem = "$livrosCriados requisição(ões) criada(s) com sucesso! Foi enviado um email de confirmação.";
            return redirect()->route('requisicoes.index')->with('success', $mensagem);
        } else {
            return back()->with('error', 'Não foi possível criar a requisição. Os livros selecionados podem já não estar disponíveis.');
        }
    }

    // =============================================
    // == MÉTODOS RESTAURADOS                       ==
    // =============================================
    public function aprovar(Requisicao $requisicao)
    {
        $requisicao->update(['status' => 'aprovado']);
        return back()->with('success', 'Requisição aprovada!');
    }

    public function entregar(Request $request, Requisicao $requisicao)
    {


        $validated = $request->validate(['data_fim_real' => 'required|date', 'observacoes' => 'nullable|string|max:500', 'estado_devolucao' => 'required|string|in:intacto,marcas_uso,danificado,nao_devolvido']);
        $dataFimReal = Carbon::parse($validated['data_fim_real']);
        $diasAtraso = $dataFimReal->isAfter($requisicao->data_fim_prevista) ? $dataFimReal->diffInDays($requisicao->data_fim_prevista) : 0;

        $requisicao->update(['status' => 'devolvido', 'data_fim_real' => $dataFimReal, 'observacoes' => $validated['observacoes'], 'dias_atraso' => $diasAtraso, 'estado_devolucao' => $validated['estado_devolucao']]);

        if (in_array($validated['estado_devolucao'], ['intacto', 'marcas_uso', 'danificado'])) {
            $livroDevolvido = $requisicao->livro;
            if ($livroDevolvido) {
                $quantidadeAntiga = $livroDevolvido->quantidade;
                $livroDevolvido->increment('quantidade');
                if ($quantidadeAntiga === 0) {
                    $alertas = AlertaDisponibilidade::where('livro_id', $livroDevolvido->id)->with('user')->get();
                    foreach ($alertas as $alerta) {
                        if ($alerta->user) {
                            // Linha CORRIGIDA
                            Mail::to($alerta->user->email)->queue(new LivroDisponivelAlerta($livroDevolvido->id, $alerta->user->id));
                        }
                        $alerta->delete();
                    }
                }
            }
        }
        $user = User::find($requisicao->user_id);
        if ($user) {
            $pontos_a_deduzir = 0;
            if ($validated['estado_devolucao'] === 'danificado') $pontos_a_deduzir = 25;
            if ($validated['estado_devolucao'] === 'nao_devolvido') $pontos_a_deduzir = 50;
            if ($pontos_a_deduzir > 0) $user->decrement('pontos', $pontos_a_deduzir);
        }
        return back()->with('success', 'Devolução registrada com sucesso!');
    }

    public function cancelar(Requisicao $requisicao)
    {
        $user = auth()->user();
        if (($user->id === $requisicao->user_id && $requisicao->status === 'solicitado') || $user->role === 'admin') {
            $requisicao->livro()->increment('quantidade'); // Devolve o livro ao estoque
            $requisicao->delete();
            return back()->with('success', 'Requisição cancelada/removida com sucesso.');
        }
        return back()->with('error', 'Você não tem permissão para esta ação.');
    }

    private function removeAcentos($string)
    {
        return strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $string ?? ''));
    }

    public function mostrarFormularioReview(Requisicao $requisicao)
    {
        if ($requisicao->user_id !== auth()->id() || $requisicao->status !== 'devolvido' || Review::where('user_id', auth()->id())->where('livro_id', $requisicao->livro_id)->exists()) {
            abort(403, 'Ação não autorizada.');
        }
        return view('reviews.create', compact('requisicao'));
    }

    public function guardarReview(Request $request, Requisicao $requisicao)
    {
        if ($requisicao->user_id !== auth()->id() || $requisicao->status !== 'devolvido' || Review::where('user_id', auth()->id())->where('livro_id', $requisicao->livro_id)->exists()) {
            abort(403, 'Ação não autorizada.');
        }
        $validated = $request->validate(['classificacao' => 'required|integer|min:1|max:5', 'comentario' => 'nullable|string|max:2000',]);
        $novaReview = Review::create(['user_id' => auth()->id(), 'livro_id' => $requisicao->livro_id, 'classificacao' => $validated['classificacao'], 'comentario' => $validated['comentario'], 'status' => 'pendente',]);
        $emailAdmin = 'regianecinel@gmail.com';
        Mail::to($emailAdmin)->queue(new NovaReviewParaAdmin($novaReview));
        return redirect()->route('requisicoes.index')->with('success', 'A sua avaliação foi submetida com sucesso e aguarda moderação!');
    }
}
