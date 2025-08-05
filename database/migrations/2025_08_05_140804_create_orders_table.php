<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Ligação à morada de entrega
            $table->foreignId('shipping_address_id')->constrained('addresses')->onDelete('cascade');

            $table->string('numero_encomenda')->unique(); // Um número único para cada encomenda (ex: ENC-2025-0001)
            $table->enum('status', ['pendente', 'pago', 'enviado', 'cancelado'])->default('pendente');

            // Valores monetários, cruciais para a fatura
            $table->decimal('subtotal', 8, 2);
            $table->decimal('taxas', 8, 2)->default(0.00);    // Ex: IVA
            $table->decimal('envio', 8, 2)->default(0.00);     // Custo de envio
            $table->decimal('total', 8, 2);

            $table->string('metodo_pagamento')->nullable(); // Ex: "stripe"
            $table->string('transacao_id')->nullable();     // ID da transação do Stripe

            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
