<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisicoes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_sequencial')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('livro_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['solicitado', 'aprovado', 'entregue', 'devolvido'])->default('solicitado');
            $table->date('data_inicio');
            $table->date('data_fim_prevista');
            $table->date('data_fim_real')->nullable();
            $table->integer('dias_atraso')->default(0);
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisicoes');
    }
};