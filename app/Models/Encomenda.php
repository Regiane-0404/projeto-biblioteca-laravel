<?php

namespace App\Models;

use App\Enums\EstadoEncomenda; // Usamos o nosso novo Enum
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Encomenda extends Model
{
    use HasFactory;
    protected $table = 'encomendas'; // Especifica o nome da tabela
    protected $guarded = []; // Permite preenchimento em massa

    protected $casts = [
        'estado' => EstadoEncomenda::class, // Usa o Enum para a coluna 'estado'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function itens(): HasMany
    {
        // O nome da chave estrangeira é 'encomenda_id'
        return $this->hasMany(EncomendaItem::class, 'encomenda_id');
    }

    public function moradaEnvio(): BelongsTo
    {
        return $this->belongsTo(Morada::class, 'morada_envio_id');
    }

    public function moradaFaturacao(): BelongsTo
    {
        return $this->belongsTo(Morada::class, 'morada_faturacao_id');
    }
}
