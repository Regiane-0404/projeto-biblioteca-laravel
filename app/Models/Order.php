<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shipping_address_id',
        'numero_encomenda',
        'status',
        'subtotal',
        'taxas',
        'envio',
        'total',
        'metodo_pagamento',
        'transacao_id',
    ];

    // Relação: Uma encomenda pertence a um utilizador
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relação: Uma encomenda tem muitos itens
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Relação: Uma encomenda tem uma morada de entrega
    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }
}
