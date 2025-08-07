<?php

namespace App\Http\Controllers;

use App\Models\Encomenda; // Importamos o modelo Encomenda
use Illuminate\Http\Request;

class AdminEncomendaController extends Controller
{
    /**
     * Mostra uma lista de todas as encomendas.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // 1. Buscar as encomendas da base de dados
        $encomendas = Encomenda::with('user') // 2. Carrega a informação do cliente para otimização
            ->latest() // 3. Ordena pelas mais recentes
            ->paginate(15); // 4. Pagina os resultados

        // 5. Retorna a view e passa a variável 'encomendas' para ela
        return view('admin.encomendas.index', compact('encomendas'));
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
}
