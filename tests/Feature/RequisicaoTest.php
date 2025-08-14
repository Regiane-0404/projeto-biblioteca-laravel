<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Livro;
use App\Models\Requisicao;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

/**
 * TESTE 1
 * Permite que um utilizador autenticado crie uma requisição de um livro com stock
 */
it('1. permite que um utilizador autenticado crie uma requisicao de um livro com stock', function () {
    Mail::fake();

    $user = User::factory()->create(['role' => 'cidadao']);
    $livro = Livro::factory()->create(['quantidade_venda' => 5]);

    $response = $this->actingAs($user)->post(route('requisicoes.store'), [
        'livros_ids' => [$livro->id],
    ]);

    $this->assertDatabaseHas('requisicoes', [
        'user_id' => $user->id,
        'livro_id' => $livro->id,
        'status' => 'solicitado'
    ]);

    $response->assertRedirect(route('requisicoes.index'));
});

/**
 * TESTE 2
 * Não permite criar uma requisição sem selecionar um livro
 */
it('2. nao permite criar uma requisicao sem selecionar um livro', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('requisicoes.store'), [
        'livros_ids' => [],
    ]);

    $this->assertDatabaseCount('requisicoes', 0);
    $response->assertSessionHasErrors('livros_ids');
});

/**
 * TESTE 3
 * Permite que um admin registe a devolução de um livro
 */
it('3. permite que um admin registe a devolucao de um livro', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'ativo' => true,
    ]);

    $livro = Livro::factory()->create(['quantidade' => 5]);
    $requisicao = Requisicao::factory()->create([
        'user_id' => User::factory(),
        'livro_id' => $livro->id,
        'status' => 'aprovado'
    ]);

    $url = '/requisicoes/' . $requisicao->id . '/entregar';

    $response = $this->actingAs($admin)->patch($url, [
        'data_fim_real' => now()->toDateString(),
        'estado_devolucao' => 'intacto',
        'observacoes' => 'Devolvido durante o teste.'
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();

    $requisicaoAtualizada = Requisicao::find($requisicao->id);

    expect($requisicaoAtualizada->status)->toBe('devolvido');

    $this->assertDatabaseHas('livros', [
        'id' => $livro->id,
        'quantidade' => 6
    ]);
});

/**
 * TESTE 4
 * Não permite devolver um livro já devolvido
 */
it('4. nao permite devolver um livro ja devolvido', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $livro = Livro::factory()->create(['quantidade' => 5]);
    $requisicao = Requisicao::factory()->create([
        'user_id' => User::factory(),
        'livro_id' => $livro->id,
        'status' => 'devolvido'
    ]);

    $response = $this->actingAs($admin)
        ->patch(route('requisicoes.entregar', $requisicao), [
            'estado_devolucao' => 'danificado',
            'observacoes' => 'Tentativa de devolução duplicada'
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('requisicoes', [
        'id' => $requisicao->id,
        'status' => 'devolvido'
    ]);
});

/**
 * TESTE 5
 * Mostra a um utilizador apenas as suas próprias requisições
 */
it('5. mostra a um utilizador apenas as suas proprias requisicoes', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    $requisicaoUserA = Requisicao::factory()->create(['user_id' => $userA->id]);
    $requisicaoUserB = Requisicao::factory()->create(['user_id' => $userB->id]);

    $response = $this->actingAs($userA)->get(route('requisicoes.index'));

    $response->assertOk();
    $response->assertSee($requisicaoUserA->numero_sequencial);
    $response->assertDontSee($requisicaoUserB->numero_sequencial);
});

// --- TESTE 5: Teste de Stock ---
it('nao permite criar uma requisicao para um livro sem stock', function () {
    // 1. Arrange
    $user = User::factory()->create();

    // Criamos um livro especificamente SEM stock.
    $livroSemStock = Livro::factory()->create(['quantidade' => 0]);

    // 2. Act
    // Tentamos criar a requisição para o livro sem stock.
    $response = $this->actingAs($user)->post(route('requisicoes.store'), [
        'livros_ids' => [$livroSemStock->id],
    ]);

    // 3. Assert
    // Verificamos se NADA foi criado na base de dados.
    $this->assertDatabaseCount('requisicoes', 0);

    // Verificamos se o utilizador foi redirecionado de volta com uma mensagem de erro.
    $response->assertRedirect();
    $response->assertSessionHas('error'); // Verifica se existe uma chave 'error' na sessão.
});
