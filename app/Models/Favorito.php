<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorito extends Model
{
    use HasFactory;

    protected $table = 'favoritos';

    protected $fillable = [
        'user_id',
        'pelicula_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pelicula()
    {
        return $this->belongsTo(Peliculas::class);
    }
}
