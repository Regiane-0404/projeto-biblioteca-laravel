<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('autors', function (Blueprint $table) {
            $table->id();
            $table->text('nome'); // Cifrado
            $table->text('foto')->nullable(); // Cifrado
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('autors');
    }
};