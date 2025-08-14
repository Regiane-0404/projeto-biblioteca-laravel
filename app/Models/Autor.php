<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Encryptable;

// --- 1. Adicionar os "use" statements necessários ---
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Autor extends Model
{
    // --- 2. Adicionar o trait "LogsActivity" ---
    use HasFactory, Encryptable, LogsActivity;

    protected $fillable = ['nome', 'foto'];
    
    protected $encryptable = ['nome', 'foto'];

    // --- O SEU CÓDIGO ORIGINAL (INTACTO) ---
    public function getNomeDescriptografadoAttribute()
    {
        try {
            return $this->nome;
        } catch (\Exception $e) {
            return $this->attributes['nome'];
        }
    }

    public function livros()
    {
        return $this->belongsToMany(Livro::class, 'autor_livro');
    }

    // =======================================================
    // ==         INÍCIO DA CONFIGURAÇÃO DO LOGGER          ==
    // =======================================================
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Autores')
            ->logOnly(['nome', 'foto'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(function(string $eventName) {
                $nomeAutor = $this->nome; // O trait Encryptable já trata da desencriptação.
                switch ($eventName) {
                    case 'created':
                        return "O autor '{$nomeAutor}' foi criado";
                    case 'updated':
                        return "O autor '{$nomeAutor}' foi atualizado";
                    case 'deleted':
                        return "O autor '{$nomeAutor}' foi apagado";
                    default:
                        return "Ação '{$eventName}' realizada no autor '{$nomeAutor}'";
                }
            });
    }
    // =======================================================
    // ==           FIM DA CONFIGURAÇÃO DO LOGGER           ==
    // =======================================================
}