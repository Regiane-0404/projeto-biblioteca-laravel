<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Encryptable;

class Livro extends Model
{
    use HasFactory, Encryptable;

    protected $fillable = [
        'isbn', 'nome', 'editora_id', 'bibliografia', 'imagem_capa', 'preco', 'ativo'
    ];

    protected $encryptable = [
        'isbn', 'nome', 'bibliografia', 'imagem_capa', 'preco'
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    // Scope para livros ativos
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    // Scope para livros inativos  
    public function scopeInativos($query)
    {
        return $query->where('ativo', false);
    }

    // Verificar se tem dependências que impedem exclusão
    public function temDependencias()
    {
        // Verificar se tem autores associados
        if ($this->autores()->count() > 0) {
            return true;
        }

      

        return false;
    }

    // Verificar se pode ser excluído
    public function podeSerExcluido()
    {
        return !$this->temDependencias();
    }

    public function editora()
    {
        return $this->belongsTo(Editora::class);
    }

    public function autores()
    {
        return $this->belongsToMany(Autor::class, 'autor_livro');
    }
}