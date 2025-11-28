<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peliculas;
use Illuminate\Support\Facades\Log;

class FavoritoController extends Controller
{
    /**
     * Obtener todas las películas favoritas del usuario autenticado
     * GET /api/favoritos
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            // Obtiene las películas favoritas con toda su información
            $favoritos = $user->favoritos()->get();

            Log::info('Favoritos obtenidos', [
                'user_id' => $user->id,
                'count' => $favoritos->count()
            ]);

            return response()->json($favoritos);

        } catch (\Exception $e) {
            Log::error('Error al obtener favoritos', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'No se pudieron obtener los favoritos'
            ], 500);
        }
    }

    /**
     * Agregar una película a favoritos
     * POST /api/favoritos/{peliculaId}
     */
    public function store(Request $request, $peliculaId)
    {
        try {
            $user = $request->user();

            // Verificar que la película existe
            $pelicula = Peliculas::findOrFail($peliculaId);

            // Verificar si ya está en favoritos
            if ($user->favoritos()->where('pelicula_id', $peliculaId)->exists()) {
                return response()->json([
                    'message' => 'La película ya está en favoritos'
                ], 200);
            }

            // Agregar a favoritos
            $user->favoritos()->attach($peliculaId);

            Log::info('Película agregada a favoritos', [
                'user_id' => $user->id,
                'pelicula_id' => $peliculaId,
                'pelicula_title' => $pelicula->title
            ]);

            return response()->json([
                'message' => 'Película agregada a favoritos'
            ], 201);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Película no encontrada'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error al agregar favorito', [
                'user_id' => $request->user()->id ?? 'unknown',
                'pelicula_id' => $peliculaId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'No se pudo agregar a favoritos'
            ], 500);
        }
    }

    /**
     * Eliminar una película de favoritos
     * DELETE /api/favoritos/{peliculaId}
     */
    public function destroy(Request $request, $peliculaId)
    {
        try {
            $user = $request->user();

            // Verificar que la película existe
            $pelicula = Peliculas::findOrFail($peliculaId);

            // Verificar si está en favoritos
            if (!$user->favoritos()->where('pelicula_id', $peliculaId)->exists()) {
                return response()->json([
                    'message' => 'La película no está en favoritos'
                ], 200);
            }

            // Eliminar de favoritos
            $user->favoritos()->detach($peliculaId);

            Log::info('Película eliminada de favoritos', [
                'user_id' => $user->id,
                'pelicula_id' => $peliculaId,
                'pelicula_title' => $pelicula->title
            ]);

            return response()->json([
                'message' => 'Película eliminada de favoritos'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Película no encontrada'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error al eliminar favorito', [
                'user_id' => $request->user()->id ?? 'unknown',
                'pelicula_id' => $peliculaId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'No se pudo eliminar de favoritos'
            ], 500);
        }
    }

    /**
     * Verificar si una película es favorita
     * GET /api/favoritos/check/{peliculaId}
     */
    public function check(Request $request, $peliculaId)
    {
        try {
            $user = $request->user();

            $isFavorite = $user->favoritos()
                ->where('pelicula_id', $peliculaId)
                ->exists();

            return response()->json([
                'is_favorite' => $isFavorite
            ]);

        } catch (\Exception $e) {
            Log::error('Error al verificar favorito', [
                'user_id' => $request->user()->id ?? 'unknown',
                'pelicula_id' => $peliculaId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'is_favorite' => false
            ]);
        }
    }
}
