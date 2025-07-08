<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('editoras', function (Blueprint $table) {
            $table->id();
            $table->text('nome'); // Cifrado
            $table->text('logotipo')->nullable(); // Cifrado
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('editoras');
    }
};