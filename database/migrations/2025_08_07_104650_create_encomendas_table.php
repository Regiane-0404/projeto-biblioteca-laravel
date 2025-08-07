<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encomendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('morada_envio_id')->constrained('moradas');
            $table->foreignId('morada_faturacao_id')->constrained('moradas');
            $table->string('numero_encomenda')->unique();
            $table->string('estado'); // Ex: 'pendente', 'pago', 'enviado'
            $table->decimal('subtotal', 8, 2);
            $table->decimal('impostos', 8, 2)->default(0);
            $table->decimal('portes_envio', 8, 2)->default(0);
            $table->decimal('total', 8, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encomendas');
    }
};
