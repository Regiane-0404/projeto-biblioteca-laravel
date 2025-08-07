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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
Route::get('/api/buscar-cp/{cp}', function ($cp) {
    $cpFormatado = Str::of($cp)->replaceMatches('/[^0-9]/', '');

    if (strlen($cpFormatado) !== 7) {
        return response()->json(['error' => 'Código Postal inválido'], 400);
    }

    $cpFormatado = substr($cpFormatado, 0, 4) . '-' . substr($cpFormatado, 4);

    try {
        $json = Storage::get('codigos_postais.json');
        $dados = json_decode($json, true);

        foreach ($dados as $item) {
            if ($item['codigo_postal'] === $cpFormatado) {
                return response()->json([
                    'street' => $item['morada'],
                    'city' => $item['localidade']
                ]);
            }
        }

        return response()->json(['error' => 'Código Postal não encontrado.'], 404);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Erro ao processar a requisição.'], 500);
    }
});

/*
|--------------------------------------------------------------------------
| Rotas protegidas por autenticação
|--------------------------------------------------------------------------
|
| Todos os usuários autenticados podem acessar estas rotas.
| O middleware 'auth:sanctum', 'verified' e sessão Jetstream já estão aplicados.
|
*/
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Livros
    Route::get('/livros', [LivroController::class, 'index'])->name('livros.index');
    Route::get('/livros/{livro}', [LivroController::class, 'show'])
        ->where('livro', '[0-9]+')
        ->name('livros.show');

    Route::post('/livros/{livro}/solicitar-alerta', [LivroController::class, 'solicitarAlerta'])->name('livros.solicitar-alerta');
    Route::delete('/livros/{livro}/cancelar-alerta', [LivroController::class, 'cancelarAlerta'])->name('livros.cancelar-alerta');

    // Autores e Editoras
    Route::get('/autores', [AutorController::class, 'index'])->name('autores.index');
    Route::get('/editoras', [EditoraController::class, 'index'])->name('editoras.index');

    // Requisições
    Route::get('/requisicoes', [RequisicaoController::class, 'index'])->name('requisicoes.index');
    Route::get('/requisicoes/create/{livro_id?}', [RequisicaoController::class, 'create'])->name('requisicoes.create');
    Route::post('/requisicoes', [RequisicaoController::class, 'store'])->name('requisicoes.store');
    Route::delete('/requisicoes/{requisicao}/cancelar', [RequisicaoController::class, 'cancelar'])->name('requisicoes.cancelar');

    // Reviews
    Route::get('/requisicoes/{requisicao}/review', [RequisicaoController::class, 'mostrarFormularioReview'])->name('reviews.create');
    Route::post('/requisicoes/{requisicao}/review', [RequisicaoController::class, 'guardarReview'])->name('reviews.store');

    /*
    |--------------------------------------------------------------------------
    | ROTAS DO PROCESSO DE CHECKOUT
    |--------------------------------------------------------------------------
    |
    | Estão dentro do grupo autenticado, mas SEM middleware aninhado extra.
    |
    */
    Route::get('/checkout/morada', [CheckoutController::class, 'mostrarFormularioMorada'])->name('checkout.morada.form');
    Route::post('/checkout/morada', [CheckoutController::class, 'guardarMoradaECriarEncomenda'])->name('checkout.morada.store');

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
