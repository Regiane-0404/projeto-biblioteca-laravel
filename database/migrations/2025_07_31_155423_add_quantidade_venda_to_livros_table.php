<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('livros', function (Blueprint $table) {
            // Adiciona a nova coluna a seguir à coluna 'quantidade' existente para organização.
            $table->unsignedInteger('quantidade_venda')->default(0)->after('quantidade');
        });
    }

    public function down(): void
    {
        Schema::table('livros', function (Blueprint $table) {
            $table->dropColumn('quantidade_venda');
        });
    }
};
