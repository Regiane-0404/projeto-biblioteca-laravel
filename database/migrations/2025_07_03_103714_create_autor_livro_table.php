<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('autor_livro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('autor_id')->constrained('autors');
            $table->foreignId('livro_id')->constrained('livros');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('autor_livro');
    }
};