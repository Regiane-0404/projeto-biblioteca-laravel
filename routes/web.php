<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\EditoraController;
use App\Http\Controllers\RequisicaoController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Rota inicial
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Rotas que exigem que o usuário esteja autenticado
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // --- ROTAS ACESSÍVEIS POR TODOS OS PERFIS LOGADOS ---

    // Dashboard (Controller decide a view)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Consulta à Biblioteca (Cidadão pode ver listas e detalhes)
    Route::get('/livros', [LivroController::class, 'index'])->name('livros.index');
    Route::get('/livros/{livro}', [LivroController::class, 'show'])->name('livros.show'); // Temporariamente desativada
    Route::get('/autores', [AutorController::class, 'index'])->name('autores.index');
    Route::get('/editoras', [EditoraController::class, 'index'])->name('editoras.index');

    // Gestão de Requisições Pessoais
    Route::get('/requisicoes', [RequisicaoController::class, 'index'])->name('requisicoes.index');
    Route::get('/requisicoes/create', [RequisicaoController::class, 'create'])->name('requisicoes.create');
    Route::post('/requisicoes', [RequisicaoController::class, 'store'])->name('requisicoes.store');
    // Rota de cancelamento que serve para Admin e Cidadão
    Route::delete('/requisicoes/{requisicao}/cancelar', [RequisicaoController::class, 'cancelar'])->name('requisicoes.cancelar');


    // --- ROTAS EXCLUSIVAS DO ADMIN ---
    Route::middleware(['admin'])->group(function () {
        
        // Gestão completa de Livros (tudo menos a consulta, que já está acima)
        Route::resource('livros', LivroController::class)->except(['index', 'show']);
        Route::patch('/livros/{livro}/inativar', [LivroController::class, 'inativar'])->name('livros.inativar');
        Route::patch('/livros/{livro}/ativar', [LivroController::class, 'ativar'])->name('livros.ativar');
        Route::get('/livros/exportar/csv', [LivroController::class, 'exportar'])->name('livros.exportar');

        // Gestão de Autores e Editoras (tudo menos a consulta)
        Route::resource('autores', AutorController::class)->except(['index', 'show']);
        Route::resource('editoras', EditoraController::class)->except(['index', 'show']);

        // Gestão de Usuários
        Route::resource('users', UserController::class)->except(['show']);
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

        // Ações de Admin em Requisições
        Route::patch('/requisicoes/{requisicao}/aprovar', [RequisicaoController::class, 'aprovar'])->name('requisicoes.aprovar');
        Route::patch('/requisicoes/{requisicao}/entregar', [RequisicaoController::class, 'entregar'])->name('requisicoes.entregar');
    });
});