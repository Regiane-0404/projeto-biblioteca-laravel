<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Encryptable;

class Editora extends Model
{
    use HasFactory, Encryptable;

    protected $fillable = ['nome', 'logotipo'];

    protected $encryptable = ['nome', 'logotipo'];

    // ADICIONE ESTE MÉTODO:
    public function getNomeDescriptografadoAttribute()
    {
        try {
            return $this->nome; // O trait vai descriptografar automaticamente
        } catch (\Exception $e) {
            return $this->attributes['nome']; // Se falhar, retorna como está
        }
    }

    public function livros()
    {
        return $this->hasMany(Livro::class);
    }
}
