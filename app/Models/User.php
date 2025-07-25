<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany; // <-- ADICIONEI ESTE IMPORT
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasProfilePhoto, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Verifique se 'role' e 'ativo' estão no seu fillable
        'ativo',
        'pontos',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'ativo' => 'boolean', // Verifique se 'ativo' está aqui
        ];
    }

    // --- RELACIONAMENTOS ---

    public function requisicoes(): HasMany // <-- ADICIONEI O TIPO DE RETORNO
    {
        return $this->hasMany(Requisicao::class);
    }

    public function requisicoesAtivas() // <-- NOME CORRIGIDO
    {
        return $this->hasMany(Requisicao::class)->whereIn('status', ['solicitado', 'aprovado']);
    }

    /**
     * Obtém todas as reviews feitas por este usuário.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Obtém os alertas de disponibilidade para este usuário.
     */
    public function alertasDisponibilidade()
    {
        return $this->hasMany(AlertaDisponibilidade::class);
    }
}
