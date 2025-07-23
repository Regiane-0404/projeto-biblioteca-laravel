<?php

namespace App\Models;

use App\Traits\Encryptable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Livro extends Model
{
    use HasFactory, Encryptable;

    protected $fillable = [
        'isbn',
        'nome',
        'editora_id',
        'bibliografia',
        'imagem_capa',
        'preco',
        'ativo',
        'quantidade', // <-- ADICIONADO
    ];

    protected $encryptable = [
        'isbn',
        'nome',
        'bibliografia',
        'imagem_capa',
        'preco'
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    // RELAÇÕES
    public function editora(): BelongsTo
    {
        return $this->belongsTo(Editora::class);
    }

    public function autores(): BelongsToMany
    {
        return $this->belongsToMany(Autor::class, 'autor_livro');
    }

    public function requisicoes(): HasMany
    {
        return $this->hasMany(Requisicao::class);
    }

    public function requisicaoAtiva(): HasOne
    {
        return $this->hasOne(Requisicao::class)->whereIn('status', ['solicitado', 'aprovado']);
    }

    // MÉTODOS HELPER
    public function isDisponivel()
    {
        // A nova regra: um livro está disponível se a sua quantidade em estoque for maior que zero.
        return $this->quantidade > 0;
    }

    /**
     * Verifica se o livro pode ser excluído.
     * Um livro só pode ser excluído se NUNCA teve uma requisição.
     */
    public function podeSerExcluido()
    {
        // O método has() verifica se a relação 'requisicoes' tem pelo menos um registo.
        // Nós retornamos o oposto: se NÃO TEM requisições, pode ser excluído.
        return !$this->requisicoes()->exists();
    }

    /**
     * Obtém todas as reviews para este livro.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
