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
        Schema::create('alertas_disponibilidade', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('livro_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Regra de segurança: impede que o mesmo usuário se inscreva
            // mais do que uma vez para receber o alerta do mesmo livro.
            $table->unique(['user_id', 'livro_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas_disponibilidade');
    }
};
