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

    /**
     * Encontra livros relacionados com base em palavras-chave na bibliografia.
     *
     * @param int $limite O número de livros relacionados a retornar.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLivrosRelacionados($limite = 4)
    {
        // Se não há bibliografia, não fazemos nada.
        if (empty($this->bibliografia)) {
            return collect();
        }

        // 1. Extrai as palavras-chave deste livro (desencriptado).
        $palavrasChave = collect(preg_split('/[\s,\.;\(\)]+/', $this->bibliografia))
            ->map(fn($palavra) => trim(strtolower($palavra)))
            ->filter(fn($palavra) => strlen($palavra) > 5)
            ->unique()
            ->take(10);

        if ($palavrasChave->isEmpty()) {
            return collect();
        }

        // 2. VAI BUSCAR TODOS OS OUTROS LIVROS À BASE DE DADOS.
        //    É menos eficiente, mas à prova de falhas com a encriptação.
        $outrosLivros = Livro::where('id', '!=', $this->id)->where('ativo', true)->get();

        // 3. AGORA, FILTRAMOS EM PHP (onde os dados já estão desencriptados).
        $livrosComPontuacao = $outrosLivros->map(function ($livro) use ($palavrasChave) {
            $pontos = 0;
            // Se o outro livro também não tem bibliografia, tem 0 pontos.
            if (empty($livro->bibliografia)) {
                $livro->pontuacao_similaridade = 0;
                return $livro;
            }

            // Compara as palavras
            foreach ($palavrasChave as $palavra) {
                if (stripos($livro->bibliografia, $palavra) !== false) {
                    $pontos++;
                }
            }
            $livro->pontuacao_similaridade = $pontos;
            return $livro;
        });

        // 4. Ordena pelos que tiveram mais correspondências e retorna o limite.
        return $livrosComPontuacao->where('pontuacao_similaridade', '>', 0) // Só queremos os que têm pelo menos 1 correspondência
            ->sortByDesc('pontuacao_similaridade')
            ->take($limite);
    }
}
