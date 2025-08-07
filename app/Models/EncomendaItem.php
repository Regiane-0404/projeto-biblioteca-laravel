<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EncomendaItem extends Model
{
    use HasFactory;
    protected $table = 'encomenda_itens'; // Especifica o nome da tabela
    protected $guarded = []; // Permite preenchimento em massa

    // Define que não vamos usar as colunas created_at/updated_at nesta tabela
    // public $timestamps = false; // Descomente se não as quiser

    public function encomenda(): BelongsTo
    {
        return $this->belongsTo(Encomenda::class, 'encomenda_id');
    }

    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }
}
