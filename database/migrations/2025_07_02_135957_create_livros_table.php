<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('livros', function (Blueprint $table) {
            $table->id();
            $table->text('isbn'); // Cifrado
            $table->text('nome'); // Cifrado
            $table->foreignId('editora_id')->constrained('editoras');
            $table->text('bibliografia')->nullable(); // Cifrado
            $table->text('imagem_capa')->nullable(); // Cifrado
            $table->text('preco'); // Cifrado
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('livros');
    }
};