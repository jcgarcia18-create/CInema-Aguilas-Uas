<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Peliculas;// ¡Asegúrate de que esta línea esté presente!

class PeliculasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Elimina todos los registros existentes para evitar duplicados en cada ejecución.
        Peliculas::truncate();

        // 2. Inserta los datos de prueba usando el método create() de Eloquent.
        Peliculas::create([
            'title' => 'El Caballero Oscuro',
            'description' => 'Tras el asesinato del fiscal Harvey Dent, Batman asume la responsabilidad de los crímenes de Dent para proteger su reputación y la esperanza que representa.',
            'poster_url' => 'https://macguffin007.com/wp-content/uploads/2018/07/El-caballero-oscuro.jpg',
            'video_url' => 'https://www.youtube.com/watch?v=kmJLuwP3MbY',
            'duration_minutes' => 152,
            'genre' => 'Acción, Crimen',
        ]);

        Peliculas::create([
            'title' => 'Parásitos',
            'description' => 'La codicia y la discriminación de clase amenazan la relación simbiótica recién formada entre la rica familia Park y la pobre familia Kim.',
            'poster_url' => 'https://poptaim.com/wp-content/uploads/2020/10/parasite_afiche.jpg',
            'video_url' => 'https://example.com/videos/parasitos.mp4',
            'duration_minutes' => 132,
            'genre' => 'Comedia negra, Thriller',
        ]);

        Peliculas::create([
            'title' => 'Dune',
            'description' => 'Un joven brillante y talentoso, Paul Atreides, debe viajar al planeta más peligroso del universo para asegurar el futuro de su familia y su pueblo.',
            'poster_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTa8U5wqsJPHbk7ZIVi51evtTQkDhSmbFKU5QuS9AVddm5VPxGa9jXBDQ0t9Z2eKfrKyq8&usqp=CAUv',
            'video_url' => 'https://example.com/videos/dune.mp4',
            'duration_minutes' => 155,
            'genre' => 'Ciencia Ficción, Aventura',
        ]);

        
    }
}