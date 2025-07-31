<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')
                ->constrained('carts')
                ->onDelete('cascade'); // Correto: Itens são apagados com o carrinho.

            // AQUI ESTÁ A CORREÇÃO IMPORTANTE:
            $table->foreignId('book_id') // A coluna pode manter o nome 'book_id' por convenção
                ->constrained('livros') // MAS deve apontar para a tabela correta: 'livros'
                ->onDelete('restrict'); // Correto: Impede apagar livro se estiver num carrinho.

            $table->unsignedInteger('quantity')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
