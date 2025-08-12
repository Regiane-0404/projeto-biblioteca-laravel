<?php

namespace App\Http\Controllers;

use App\Models\Encomenda; // Importamos o modelo Encomenda
use Illuminate\Http\Request;
use App\Enums\EstadoEncomenda;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminEncomendaController extends Controller
{
    /**
     * Mostra uma lista de todas as encomendas.
     *
     * @return \Illuminate\View\View
     */

    // Ficheiro: app/Http/Controllers/AdminEncomendaController.php

    public function index(Request $request)
    {
        // Inicia a query base, ordenando sempre pelas mais recentes.
        $query = Encomenda::with('user')->latest();

        // Aplica os filtros se eles vierem no pedido.
        if ($request->filled('data_de')) {
            $query->whereDate('created_at', '>=', $request->data_de);
        }
        if ($request->filled('data_ate')) {
            $query->whereDate('created_at', '<=', $request->data_ate);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Guarda os filtros para os passar para a view (para a paginação e para manter os campos preenchidos).
        $filtros = $request->only(['data_de', 'data_ate', 'estado']);

        // =======================================================
        // ==         INÍCIO DA LÓGICA DE DECISÃO FINAL         ==
        // =======================================================
        // Verifica se algum dos filtros foi preenchido.
        // array_filter remove chaves com valores vazios (null, '', false).
        // Se o array resultante estiver vazio, significa que nenhum filtro foi aplicado.
        if (empty(array_filter($filtros))) {
            // SEM FILTROS: Pega apenas as 7 encomendas mais recentes.
            $encomendas = $query->take(7)->get();
        } else {
            // COM FILTROS: Pagina todos os resultados que correspondem.
            $encomendas = $query->paginate(15);
        }
        // =======================================================
        // ==           FIM DA LÓGICA DE DECISÃO FINAL          ==
        // =======================================================

        return view('admin.encomendas.index', compact('encomendas', 'filtros'));
    }
    /**
     * Mostra os detalhes de uma encomenda específica.
     *
     * @param  \App\Models\Encomenda  $encomenda
     * @return \Illuminate\View\View
     */
    /**
     * Mostra os detalhes de uma encomenda específica.
     *
     * @param  \App\Models\Encomenda  $encomenda
     * @return \Illuminate\View\View
     */
    public function show(Encomenda $encomenda)
    {
        // 1. Carregamos as relações de que precisamos, como antes.
        $encomenda->load(['user', 'itens.livro', 'moradaEnvio']);



        // Iteramos sobre cada item da encomenda para aceder ao livro.
        $encomenda->itens->each(function ($item) {
            // Verificamos se o livro associado ao item ainda existe.
            if ($item->livro) {
                // Ao aceder às propriedades aqui, forçamos o model a usar
                // os seus "accessors" ou "casts" para desencriptar os valores
                // antes de serem enviados para a view.
                $item->livro->nome = $item->livro->nome;
                $item->livro->imagem_capa = $item->livro->imagem_capa;
            }
        });



        // 3. Retornamos a view, agora com os dados do livro já desencriptados.
        return view('admin.encomendas.show', compact('encomenda'));
    }

    public function marcarComoPago(Encomenda $encomenda)
    {
        // Apenas permite a ação se a encomenda estiver pendente
        if ($encomenda->estado->value === 'pendente') {
            $encomenda->estado = EstadoEncomenda::PAGO;
            $encomenda->save();
            return back()->with('success', 'Encomenda marcada como Paga com sucesso!');
        }
        return back()->with('error', 'Ação inválida. A encomenda não se encontra pendente.');
    }

    /**
     * Marca uma encomenda como ENVIADA.
     */
    public function marcarComoEnviada(Encomenda $encomenda)
    {
        // Apenas permite a ação se a encomenda estiver paga
        if ($encomenda->estado->value === 'pago') {
            $encomenda->estado = EstadoEncomenda::ENVIADO;
            $encomenda->save();
            return back()->with('success', 'Encomenda marcada como Enviada com sucesso!');
        }
        return back()->with('error', 'Ação inválida. A encomenda precisa de estar paga para ser enviada.');
    }
    public function cancelarEncomenda(Encomenda $encomenda)
    {
        // Apenas permite cancelar se a encomenda NÃO estiver já enviada ou cancelada.
        if (!in_array($encomenda->estado->value, ['enviado', 'cancelado'])) {
            $encomenda->estado = EstadoEncomenda::CANCELADO;
            $encomenda->save();
            // TODO: Adicionar lógica para repor o stock dos livros.
            return back()->with('success', 'Encomenda cancelada com sucesso!');
        }
        return back()->with('error', 'Ação inválida. Esta encomenda já não pode ser cancelada.');
    }

    public function gerarFaturaPDF(Encomenda $encomenda)
    {
        // 1. Carregamos as relações necessárias para a fatura.
        // Isto otimiza a busca na base de dados.
        $encomenda->load(['user', 'itens.livro', 'moradaFaturacao']);

        // 2. Passamos a encomenda para a view da fatura e geramos o PDF.
        $pdf = PDF::loadView('admin.encomendas.fatura', compact('encomenda'));

        // 3. Definimos o nome do ficheiro para o download.
        $nomeFicheiro = 'fatura-' . $encomenda->numero_encomenda . '.pdf';

        // 4. Forçamos o download do PDF no browser do administrador.
        return $pdf->download($nomeFicheiro);
    }
}
