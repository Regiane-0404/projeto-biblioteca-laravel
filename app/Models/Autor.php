<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Encryptable;

class Autor extends Model
{
    use HasFactory, Encryptable;

    protected $fillable = ['nome', 'foto'];
    
    protected $encryptable = ['nome', 'foto'];

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
        return $this->belongsToMany(Livro::class, 'autor_livro');
    }
}