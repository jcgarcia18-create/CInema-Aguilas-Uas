<?php

namespace App\Models;

// 1.  Usa el modelo base de MongoDB
use MongoDB\Laravel\Eloquent\Model;

class HistorialVista extends Model
{
    // 2.¡IMPORTANTE! Especifica la conexión a MongoDB
   
    protected $connection = 'mongodb';
    
    // 3. Especifica el nombre de la colección
    protected $collection = 'historial_vistas';

    // 4. Activa los timestamps (created_at y updated_at)
    public $timestamps = true; 

    // 5. Define los campos que se pueden guardar
    protected $fillable = [
        'perfil_id',      // El _id del perfil de MongoDB
        'pelicula_id',    // El id de la película de PostgreSQL
        'nombre_pelicula',// El nombre de la película reproducida
    ];
}
