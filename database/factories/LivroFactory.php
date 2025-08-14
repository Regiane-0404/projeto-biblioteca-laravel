<?php

namespace Database\Factories;

use App\Models\Editora; // Precisamos de uma editora para associar
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Livro>
 */
class LivroFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'isbn' => $this->faker->unique()->isbn13(),
            'nome' => ucwords(implode(' ', $this->faker->words(3))), // Gera um título de 3 palavras

            // Para a editora, a factory irá criar uma nova se não existir.
            'editora_id' => Editora::factory(),

            'bibliografia' => $this->faker->paragraph(),
            'imagem_capa' => 'livros/placeholder.jpg', // Usamos um caminho fixo para simplicidade
            'preco' => $this->faker->randomFloat(2, 5, 50), // Preço entre 5.00 e 50.00
            'ativo' => true,
            'quantidade' => $this->faker->numberBetween(10, 100),
            'quantidade_venda' => $this->faker->numberBetween(1, 10),
        ];
    }
}
