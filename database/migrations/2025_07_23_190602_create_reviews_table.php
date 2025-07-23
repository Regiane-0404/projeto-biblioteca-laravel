<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('livro_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('classificacao'); // Classificação de 1 a 5
            $table->text('comentario')->nullable();
            $table->enum('status', ['pendente', 'aprovado', 'recusado'])->default('pendente');
            $table->text('justificacao_recusa')->nullable();
            $table->timestamps();

            // Garante que um usuário só pode avaliar um livro uma única vez
            $table->unique(['user_id', 'livro_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
