<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    /**
     * Indica que este modelo não tem timestamps (created_at, updated_at).
     */
    public $timestamps = false;

    /**
     * Os atributos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'cart_id',
        'book_id',
        'quantity',
    ];

    /**
     * A que carrinho este item pertence.
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * O livro associado a este item do carrinho.
     * Usamos 'book_id' para corresponder à coluna da chave estrangeira.
     */
    public function livro(): BelongsTo
    {
        // Note que estamos a ligar ao modelo 'Livro'
        return $this->belongsTo(Livro::class, 'book_id');
    }
}
