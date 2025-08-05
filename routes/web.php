<?php

use App\Http\Controllers\{
    DashboardController,
    LivroController,
    AutorController,
    EditoraController,
    RequisicaoController,
    ReviewController,
    UserController,
    HomeController,
    CartController,
    CheckoutController
};
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| Rota Inicial
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| Rotas do Carrinho de Compras (Públicas)
|--------------------------------------------------------------------------
*/
Route::get('/carrinho', [CartController::class, 'index'])->name('cart.index');
Route::post('/carrinho/adicionar/{livro}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/carrinho/atualizar/{item}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/carrinho/remover/{item}', [CartController::class, 'remove'])->name('cart.remove');

/*
|--------------------------------------------------------------------------
| ROTA DE API - Consulta de Código Postal
|--------------------------------------------------------------------------
*/
// Substitua a sua rota de API por esta versão melhorada
Route::get('/api/buscar-cp/{cp}', function ($cp) {
    // Validação simples do CP
    $cp = preg_replace('/[^0-9]/', '', $cp);
    if (strlen($cp) !== 7) {
        return response()->json(['error' => 'Código Postal inválido'], 400);
    }

    // Chamada segura do nosso servidor para a API externa
    $response = Http::get("https://api.duminio.com/v1/cep/{$cp}");

    // ***** INÍCIO DA MUDANÇA *****
    // VERIFICA se a chamada foi bem-sucedida (código de status 2xx)
    if ($response->successful()) {
        // Se sim, retorna a resposta da API como JSON
        return $response->json();
    }
    // ***** FIM DA MUDANÇA *****

    // Se a chamada falhou, devolvemos o nosso próprio erro em formato JSON
    // para que o nosso front-end o possa entender.
    return response()->json([
        'error' => 'Não foi possível obter os dados do código postal.',
        'api_status' => $response->status() // Opcional: envia o status da API externa para depuração
    ], 502); // 502 Bad Gateway é um bom código para este tipo de erro
});

/*
|--------------------------------------------------------------------------
| Rotas protegidas por autenticação
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | ROTAS ACESSÍVEIS POR TODOS OS USUÁRIOS LOGADOS
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Livros (consulta)
    Route::get('/livros', [LivroController::class, 'index'])->name('livros.index');
    Route::get('/livros/{livro}', [LivroController::class, 'show'])->where('livro', '[0-9]+')->name('livros.show');

    Route::post('/livros/{livro}/solicitar-alerta', [LivroController::class, 'solicitarAlerta'])->name('livros.solicitar-alerta');
    Route::delete('/livros/{livro}/cancelar-alerta', [LivroController::class, 'cancelarAlerta'])->name('livros.cancelar-alerta');

    // Autores e Editoras (consulta)
    Route::get('/autores', [AutorController::class, 'index'])->name('autores.index');
    Route::get('/editoras', [EditoraController::class, 'index'])->name('editoras.index');

    // Requisições
    Route::get('/requisicoes', [RequisicaoController::class, 'index'])->name('requisicoes.index');
    Route::get('/requisicoes/create/{livro_id?}', [RequisicaoController::class, 'create'])->name('requisicoes.create');
    Route::post('/requisicoes', [RequisicaoController::class, 'store'])->name('requisicoes.store');
    Route::delete('/requisicoes/{requisicao}/cancelar', [RequisicaoController::class, 'cancelar'])->name('requisicoes.cancelar');

    // Reviews (submissão pelo Cidadão)
    Route::get('/requisicoes/{requisicao}/review', [RequisicaoController::class, 'mostrarFormularioReview'])->name('reviews.create');
    Route::post('/requisicoes/{requisicao}/review', [RequisicaoController::class, 'guardarReview'])->name('reviews.store');

    /*
    |--------------------------------------------------------------------------
    | ROTAS DO PROCESSO DE CHECKOUT
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth'])->group(function () {
        // Passo 1: Mostra o formulário da morada
        Route::get('/checkout/morada', [CheckoutController::class, 'mostrarFormularioMorada'])->name('checkout.morada.form');

        // Passo 2: Guarda a morada e cria a encomenda
        Route::post('/checkout/morada', [CheckoutController::class, 'guardarMoradaECriarEncomenda'])->name('checkout.morada.store');
    });

    /*
    |--------------------------------------------------------------------------
    | ROTAS EXCLUSIVAS PARA ADMIN
    |--------------------------------------------------------------------------
    */
    Route::middleware(['admin'])->group(function () {

        // Gestão de Livros
        Route::get('/livros/create', [LivroController::class, 'create'])->name('livros.create');
        Route::post('/livros', [LivroController::class, 'store'])->name('livros.store');
        Route::get('/livros/{livro}/edit', [LivroController::class, 'edit'])->name('livros.edit');
        Route::put('/livros/{livro}', [LivroController::class, 'update'])->name('livros.update');
        Route::delete('/livros/{livro}', [LivroController::class, 'destroy'])->name('livros.destroy');
        Route::patch('/livros/{livro}/inativar', [LivroController::class, 'inativar'])->name('livros.inativar');
        Route::patch('/livros/{livro}/ativar', [LivroController::class, 'ativar'])->name('livros.ativar');
        Route::get('/livros/exportar/csv', [LivroController::class, 'exportar'])->name('livros.exportar');
        Route::get('/livros/importar', [LivroController::class, 'mostrarFormularioImportacao'])->name('livros.importar.form');
        Route::get('/livros/importar/pesquisar', [LivroController::class, 'pesquisarNaGoogleAPI'])->name('livros.importar.pesquisar');
        Route::post('/livros/importar', [LivroController::class, 'guardarLivroImportado'])->name('livros.importar.store');

        // Gestão de Autores
        Route::resource('autores', AutorController::class)->except(['index', 'show']);

        // Gestão de Editoras
        Route::resource('editoras', EditoraController::class)->except(['index', 'show']);

        // Gestão de Usuários
        Route::resource('users', UserController::class);
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

        // Gestão de Requisições
        Route::patch('/requisicoes/{requisicao}/aprovar', [RequisicaoController::class, 'aprovar'])->name('requisicoes.aprovar');
        Route::patch('/requisicoes/{requisicao}/entregar', [RequisicaoController::class, 'entregar'])->name('requisicoes.entregar');

        // Gestão de Reviews
        Route::get('/reviews', [ReviewController::class, 'index'])->name('admin.reviews.index');
        Route::patch('/reviews/{review}/aprovar', [ReviewController::class, 'aprovar'])->name('admin.reviews.aprovar');
        Route::get('/reviews/{review}/recusar', [ReviewController::class, 'mostrarFormularioRecusa'])->name('admin.reviews.recusar.form');
        Route::patch('/reviews/{review}/recusar', [ReviewController::class, 'recusar'])->name('admin.reviews.recusar.submit');
    });
});
