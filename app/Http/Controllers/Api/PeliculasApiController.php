<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peliculas;
use App\Models\HistorialVista;
use App\Models\User;

class PeliculasApiController extends Controller
{
public function index(Request $request)
    {
        if ($request->has('perfil_id')) {

            $perfilId = $request->input('perfil_id');

            //  Busca en MongoDB el historial de ESE perfil
            $historial = HistorialVista::where('perfil_id', $perfilId)
                                        ->orderBy('updated_at', 'desc')
                                        ->take(10)
                                        ->pluck('pelicula_id'); // IDs de Postgres

            if ($historial->count() == 0) {
                return response()->json([]); // Devuelve un array vacío si no hay historial
            }

            // 3. Busca las películas en Postgres y las reordena
            $peliculas = Peliculas::findMany($historial)
                                  ->sortBy(function ($pelicula) use ($historial) {
                                      return array_search($pelicula->id, $historial->toArray());
                                  });

            // 4. Devuelve la lista ordenada
            return response()->json($peliculas->values());
        }



        // --- Lógica de Género
        $query = Peliculas::query();
        if ($request->has('genre')) {
            $genre = $request->input('genre');
            $query->where('genre', 'LIKE', "%$genre%");
        }

        // --- Devuelve todas las películas si no hay filtros ---
        return response()->json($query->get());
    }

    // GET /api/peliculas/{id}
    public function show($id)
    {
        // 1. Buscar la película
        $pelicula = Peliculas::find($id);

        if (!$pelicula) {
            return response()->json(['error' => 'Película no encontrada'], 404);
        }

        // 2. Verificar "silenciosamente" si hay un usuario logueado
        // auth('sanctum')->user() devuelve el objeto User si el token es válido,
        // o null si es un invitado. NO lanza error 401.
        $user = auth('sanctum')->user();

        // 3. Calcular si es favorita
        $esFavorita = false;

        if ($user) {
            // Si hay usuario, preguntamos a la base de datos si existe la relación
            // Asumimos que en tu modelo User tienes la relación 'favoritos()'
            $esFavorita = $user->favoritos()->where('pelicula_id', $id)->exists();
        }

        // 4. Inyectar el campo extra en el JSON de respuesta
        // Esto agrega "es_favorita": true/false al objeto JSON que recibe Android
        $pelicula->setAttribute('es_favorita', $esFavorita);

        return response()->json($pelicula);
    }
}
