<?php

namespace App\Http\Controllers;

use App\Models\Requisicao;
use App\Models\Livro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\RequisicaoCriada;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class RequisicaoController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $stats = [];

        if ($user->role === 'admin') {
            $stats = [
                'ativas' => Requisicao::whereIn('status', ['solicitado', 'aprovado'])->count(),
                'ultimos_30_dias' => Requisicao::where('created_at', '>=', now()->subDays(30))->count(),
                'devolvidos_hoje' => Requisicao::where('status', 'devolvido')->whereDate('data_fim_real', today())->count(),
            ];
            $query = Requisicao::query();
            if ($request->filled('status')) $query->where('status', $request->status);
            if ($request->filled('data_de')) $query->whereDate('created_at', '>=', $request->data_de);
            if ($request->filled('data_ate')) $query->whereDate('created_at', '<=', $request->data_ate);
            $requisicoes = $query->with(['user', 'livro'])->latest()->paginate(10)->withQueryString();
        } else {
            $requisicoes = $user->requisicoes()->with(['livro'])->latest()->paginate(10)->withQueryString();
        }

        $requisicoes->getCollection()->each(function ($r) {
            if ($r->livro) $r->livro->nome_visivel = $r->livro->nome;
        });

        return view('requisicoes.index', [
            'requisicoes' => $requisicoes,
            'stats' => $stats,
            'filtro_status' => $request->status,
            'filtro_data_de' => $request->data_de,
            'filtro_data_ate' => $request->data_ate,
            'active_tab' => $request->hasAny(['status', 'data_de', 'data_ate', 'tab']) ? 'lista' : 'visao_geral',
        ]);
    }

    public function create(Request $request)
    {
        $search = $request->get('search', '');

        $query = Livro::whereDoesntHave('requisicaoAtiva')->with(['editora', 'autores']);
        $todosLivros = $query->get();

        $todosLivros->each(function ($livro) {
            $livro->nome_visivel = $livro->nome;
            if ($livro->editora) {
                $livro->editora->nome_visivel = $livro->editora->nome;
            }
            $livro->autores->each(function ($autor) {
                $autor->nome_visivel = $autor->nome;
            });
        });

        $livrosFiltrados = $todosLivros;
        if ($search) {
            $livrosFiltrados = $todosLivros->filter(function ($livro) use ($search) {
                $termo = $this->removeAcentos($search);
                if (str_contains($this->removeAcentos($livro->nome_visivel), $termo)) return true;
                if ($livro->editora && str_contains($this->removeAcentos($livro->editora->nome_visivel), $termo)) return true;
                foreach ($livro->autores as $autor) {
                    if (str_contains($this->removeAcentos($autor->nome_visivel), $termo)) return true;
                }
                return false;
            });
        }

        $livrosOrdenados = $livrosFiltrados->sortBy('nome_visivel');
        $page = Paginator::resolveCurrentPage('page');
        $perPage = 12;
        $livrosDisponiveis = new LengthAwarePaginator(
            $livrosOrdenados->forPage($page, $perPage)->values(),
            $livrosOrdenados->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath()]
        );
        $livrosDisponiveis->withQueryString();

        return view('requisicoes.create', compact('livrosDisponiveis', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'livros_ids' => 'required|array|min:1|max:3',
            'livros_ids.*' => 'exists:livros,id',
        ], ['livros_ids.required' => 'Você precisa de selecionar pelo menos um livro.']);

        $user = Auth::user();
        if ($user->requisicoesAtivas()->count() + count($validated['livros_ids']) > 3) {
            return back()->with('error', 'Limite de 3 requisições ativas atingido.');
        }

        $livrosCriados = 0;
        foreach ($validated['livros_ids'] as $livro_id) {
            $livro = Livro::find($livro_id);
            if ($livro && $livro->isDisponivel()) {
                $novaRequisicao = Requisicao::create([
                    'user_id' => $user->id,
                    'livro_id' => $livro_id,
                    'data_inicio' => now(),
                    'data_fim_prevista' => now()->addDays(5)
                ]);

                // =============================================
                //   DECREMENTA O ESTOQUE DO LIVRO
                // =============================================
                $livro->decrement('quantidade');

                Mail::to($user->email)->queue(new RequisicaoCriada($novaRequisicao));
                Mail::to('regianecinel@gmail.com')->queue(new \App\Mail\NovaRequisicaoParaAdmin($novaRequisicao));

                $livrosCriados++;
            }
        }

        return redirect()->route('requisicoes.index')->with('success', "$livrosCriados requisição(ões) criada(s) com sucesso! Foi enviado um email de confirmação.");
    }

    public function aprovar(Requisicao $requisicao)
    {
        $requisicao->update(['status' => 'aprovado']);
        return back()->with('success', 'Requisição aprovada!');
    }

    public function entregar(Request $request, Requisicao $requisicao)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Ação não autorizada.');
        }

        $validated = $request->validate([
            'data_fim_real' => 'required|date',
            'observacoes' => 'nullable|string|max:500',
            'estado_devolucao' => 'required|string|in:intacto,marcas_uso,danificado,nao_devolvido',
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
            'estado_devolucao' => $validated['estado_devolucao'],
        ]);

        // =============================================
        //   INCREMENTA O ESTOQUE DO LIVRO DEVOLVIDO
        // =============================================
        $requisicao->livro->increment('quantidade');

        return back()->with('success', 'Devolução registrada com sucesso!');
    }

    public function cancelar(Requisicao $requisicao)
    {
        $user = auth()->user();
        if (($user->id === $requisicao->user_id && $requisicao->status === 'solicitado') || $user->role === 'admin') {
            $requisicao->delete();
            return back()->with('success', 'Requisição cancelada/removida com sucesso.');
        }
        return back()->with('error', 'Você não tem permissão para esta ação.');
    }

    private function removeAcentos($string)
    {
        return strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $string ?? ''));
    }
}
