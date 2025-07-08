<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Http\Controllers\LivroController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Rota de exportação ANTES do resource
    Route::get('livros/exportar', [LivroController::class, 'exportar'])->name('livros.exportar');

    // Rotas para Livros
    Route::resource('livros', \App\Http\Controllers\LivroController::class);
    Route::patch('livros/{livro}/ativar', [LivroController::class, 'ativar'])->name('livros.ativar'); 
    Route::patch('/livros/{livro}/inativar', [LivroController::class, 'inativar'])->name('livros.inativar');
    
    // Rotas para Autores  
    Route::resource('autores', \App\Http\Controllers\AutorController::class)->parameters([
        'autores' => 'autor'
    ]);
    
    // Rotas para Editoras
    Route::resource('editoras', \App\Http\Controllers\EditoraController::class)->parameters([
        'editoras' => 'editora'
    ]);
});

require __DIR__.'/auth.php';