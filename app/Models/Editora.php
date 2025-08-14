<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Encryptable;

// --- 1. Adicionar os "use" statements necessários ---
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Editora extends Model
{
    // --- 2. Adicionar o trait "LogsActivity" ---
    use HasFactory, Encryptable, LogsActivity;

    protected $fillable = ['nome', 'logotipo'];

    protected $encryptable = ['nome', 'logotipo'];

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
        return $this->hasMany(Livro::class);
    }

    // =======================================================
    // ==         INÍCIO DA CONFIGURAÇÃO DO LOGGER          ==
    // =======================================================
    /**
     * Configura as opções para o log de atividades.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            // Define o nome do "módulo" para os logs deste modelo
            ->useLogName('Editoras')
            
            // Regista apenas as alterações nos campos especificados
            ->logOnly(['nome', 'logotipo'])
            
            // Regista apenas se os campos forem realmente alterados (evita logs desnecessários)
            ->logOnlyDirty()
            
            // Não guarda o log se os atributos estiverem vazios
            ->dontSubmitEmptyLogs()
            
            // Cria uma descrição personalizada para cada evento
            ->setDescriptionForEvent(function(string $eventName) {
                // Acede ao nome desencriptado
                $nomeEditora = $this->nome; 

                switch ($eventName) {
                    case 'created':
                        return "A editora '{$nomeEditora}' foi criada";
                    case 'updated':
                        return "A editora '{$nomeEditora}' foi atualizada";
                    case 'deleted':
                        return "A editora '{$nomeEditora}' foi apagada";
                    default:
                        return "Ação '{$eventName}' realizada na editora '{$nomeEditora}'";
                }
            });
    }
    // =======================================================
    // ==           FIM DA CONFIGURAÇÃO DO LOGGER           ==
    // =======================================================
}