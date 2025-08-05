<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'livro_id',
        'quantity',
        'price',
    ];

    // Relação: Um item pertence a uma encomenda
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Relação: Um item está associado a um livro
    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }
}
