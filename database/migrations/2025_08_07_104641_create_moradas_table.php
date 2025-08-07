<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moradas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nome_completo');
            $table->string('morada');
            $table->string('complemento')->nullable();
            $table->string('codigo_postal', 8); // Ex: "1234-567"
            $table->string('localidade');
            $table->string('pais');
            $table->string('nif', 9)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moradas');
    }
};
