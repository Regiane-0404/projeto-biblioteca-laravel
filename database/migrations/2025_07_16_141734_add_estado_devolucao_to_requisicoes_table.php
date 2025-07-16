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
        Schema::table('requisicoes', function (Blueprint $table) {
            // Adicionamos a nova coluna a seguir à coluna 'observacoes'
            $table->string('estado_devolucao')->nullable()->after('observacoes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requisicoes', function (Blueprint $table) {
            $table->dropColumn('estado_devolucao');
        });
    }
};
