<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;  // Precisamos de importar os modelos relacionados
use App\Models\Livro;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Requisicao>
 */
class RequisicaoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Por defeito, cria um utilizador e um livro novos para cada requisição.
            'user_id' => User::factory(),
            'livro_id' => Livro::factory(),

            // Define valores padrão para as colunas.
            'data_inicio' => now(),
            'data_fim_prevista' => now()->addDays(5),
            'status' => 'solicitado', // O estado mais comum por defeito
        ];
    }
}
