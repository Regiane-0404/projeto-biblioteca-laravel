<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // Adiciona a nova coluna, que pode ser nula.
            // Guardará a data e hora de quando o lembrete foi enviado.
            $table->timestamp('lembrete_enviado_em')->nullable()->after('updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // Remove a coluna se precisarmos de reverter a migration.
            $table->dropColumn('lembrete_enviado_em');
        });
    }
};
