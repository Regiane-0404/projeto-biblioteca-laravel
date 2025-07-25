<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertaDisponibilidade extends Model
{
    use HasFactory;

    /**
     * O nome da tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'alertas_disponibilidade';

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'livro_id',
    ];

    /**
     * Obtém o usuário associado ao alerta.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtém o livro associado ao alerta.
     */
    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }
}
