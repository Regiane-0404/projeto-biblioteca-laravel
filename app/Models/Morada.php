<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Morada extends Model
{
    use HasFactory;
    protected $table = 'moradas'; // Especifica o nome da tabela
    protected $guarded = []; // Permite preenchimento em massa

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
