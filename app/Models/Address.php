<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;

    // Define as colunas que podem ser preenchidas em massa
    protected $fillable = [
        'user_id',
        'nome_completo',
        'morada',
        'complemento',
        'codigo_postal',
        'localidade',
        'pais',
        'nif',
    ];

    // Define a relação: Uma morada pertence a um utilizador
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
