<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'suscripcion_activa',
        'suscripcion_expira',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'suscripcion_activa' => 'boolean',
            'suscripcion_expira' => 'datetime',
        ];
    }

    // ⭐ NUEVA RELACIÓN: Favoritos
    public function favoritos()
    {
        return $this->belongsToMany(
            Peliculas::class,
            'favoritos',
            'user_id',
            'pelicula_id'
        )->withTimestamps();
    }
}
