<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Adicionar coluna ativo
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('ativo')->default(true)->after('role');
        });
        
        // Ativar usuário admin principal
        DB::table('users')
            ->where('email', 'regiane.lopes@trainee.inovcorp.com')
            ->update(['ativo' => true]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ativo');
        });
    }
};