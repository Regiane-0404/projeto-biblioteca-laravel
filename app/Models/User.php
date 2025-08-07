<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Order; // Este 'use' já estava correto
use App\Models\Morada;
use App\Models\Encomenda;

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
        'role',
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
            'ativo' => 'boolean',
        ];
    }

    // --- RELACIONAMENTOS ---

    public function requisicoes(): HasMany
    {
        return $this->hasMany(Requisicao::class);
    }

    public function requisicoesAtivas()
    {
        return $this->hasMany(Requisicao::class)->whereIn('status', ['solicitado', 'aprovado']);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function alertasDisponibilidade()
    {
        return $this->hasMany(AlertaDisponibilidade::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class)->withDefault();
    }


    public function moradas(): HasMany
    {
        return $this->hasMany(Morada::class);
    }


    public function encomendas(): HasMany
    {
        return $this->hasMany(Encomenda::class);
    }
}
