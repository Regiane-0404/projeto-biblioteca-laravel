<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'livro_id',
        'classificacao',
        'comentario',
        'status',
        'justificacao_recusa',
    ];

    /**
     * Obtém o usuário (Cidadão) que escreveu a review.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtém o livro que foi avaliado.
     */
    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }
}
