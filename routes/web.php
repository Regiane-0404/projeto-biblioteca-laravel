<?php

use App\Http\Controllers\{
    DashboardController,
    LivroController,
    AutorController,
    EditoraController,
    RequisicaoController,
    UserController
};
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


/*
|--------------------------------------------------------------------------
| Rota Inicial
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
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

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Livros (consulta geral e detalhes)
    Route::get('/livros', [LivroController::class, 'index'])->name('livros.index');
    Route::get('/livros/{livro}', [LivroController::class, 'show'])
        ->where('livro', '[0-9]+') // Evita conflito com "create"
        ->name('livros.show');

    // Autores e Editoras (visualização pública)
    Route::get('/autores', [AutorController::class, 'index'])->name('autores.index');
    Route::get('/editoras', [EditoraController::class, 'index'])->name('editoras.index');

    // Requisições do próprio usuário
    Route::get('/requisicoes', [RequisicaoController::class, 'index'])->name('requisicoes.index');
    Route::get('/requisicoes/create', [RequisicaoController::class, 'create'])->name('requisicoes.create');
    Route::post('/requisicoes', [RequisicaoController::class, 'store'])->name('requisicoes.store');
    Route::delete('/requisicoes/{requisicao}/cancelar', [RequisicaoController::class, 'cancelar'])->name('requisicoes.cancelar');


    /*
    |--------------------------------------------------------------------------
    | ROTAS EXCLUSIVAS PARA ADMIN
    |--------------------------------------------------------------------------
    */
    Route::middleware(['admin'])->group(function () {

        // LIVROS (gestão completa, exceto index/show que já estão acima)
        Route::get('/livros/create', [LivroController::class, 'create'])->name('livros.create');
        Route::post('/livros', [LivroController::class, 'store'])->name('livros.store');
        Route::get('/livros/{livro}/edit', [LivroController::class, 'edit'])->name('livros.edit');
        Route::put('/livros/{livro}', [LivroController::class, 'update'])->name('livros.update');
        Route::delete('/livros/{livro}', [LivroController::class, 'destroy'])->name('livros.destroy');
        Route::patch('/livros/{livro}/inativar', [LivroController::class, 'inativar'])->name('livros.inativar');
        Route::patch('/livros/{livro}/ativar', [LivroController::class, 'ativar'])->name('livros.ativar');
        Route::get('/livros/exportar/csv', [LivroController::class, 'exportar'])->name('livros.exportar');

        // AUTORES
        Route::get('/autores/create', [AutorController::class, 'create'])->name('autores.create');
        Route::post('/autores', [AutorController::class, 'store'])->name('autores.store');
        Route::get('/autores/{autor}/edit', [AutorController::class, 'edit'])->name('autores.edit');
        Route::put('/autores/{autor}', [AutorController::class, 'update'])->name('autores.update');
        Route::delete('/autores/{autor}', [AutorController::class, 'destroy'])->name('autores.destroy');

        // EDITORAS
        Route::get('/editoras/create', [EditoraController::class, 'create'])->name('editoras.create');
        Route::post('/editoras', [EditoraController::class, 'store'])->name('editoras.store');
        Route::get('/editoras/{editora}/edit', [EditoraController::class, 'edit'])->name('editoras.edit');
        Route::put('/editoras/{editora}', [EditoraController::class, 'update'])->name('editoras.update');
        Route::delete('/editoras/{editora}', [EditoraController::class, 'destroy'])->name('editoras.destroy');

        // USUÁRIOS
        Route::resource('users', UserController::class);
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

        // Ações administrativas em requisições
        Route::patch('/requisicoes/{requisicao}/aprovar', [RequisicaoController::class, 'aprovar'])->name('requisicoes.aprovar');
        Route::patch('/requisicoes/{requisicao}/entregar', [RequisicaoController::class, 'entregar'])->name('requisicoes.entregar');
    });
 
});
