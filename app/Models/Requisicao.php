<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Requisicao extends Model
{
    use HasFactory;

    protected $table = 'requisicoes';

    protected $fillable = [
        'numero_sequencial', 'user_id', 'livro_id', 'status', 
        'data_inicio', 'data_fim_prevista', 'data_fim_real', 
        'dias_atraso', 'observacoes'
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim_prevista' => 'date',
        'data_fim_real' => 'date',
    ];

    // RELAÇÕES
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }

    // BOOT METHOD
    public static function boot()
    {
        parent::boot();

        static::creating(function ($requisicao) {
            $ultimoNumero = self::max('id') ?? 0;
            $proximoNumero = $ultimoNumero + 1;
            $requisicao->numero_sequencial = 'REQ-' . str_pad($proximoNumero, 6, '0', STR_PAD_LEFT);
        });
    }
}