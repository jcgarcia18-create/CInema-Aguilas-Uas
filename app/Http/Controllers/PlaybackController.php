<?php

namespace App\Http\Controllers;

use App\Models\HistorialVista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException; // <-- Importa la excepción

class PlaybackController extends Controller
{
    public function registrarVista(Request $request)
    {
        Log::info('PlaybackController: Se recibió una petición de logView.');

        $perfilId = session('active_profile_id');

        if (!$perfilId) {
            Log::warning('PlaybackController: Fallo. No hay perfil activo en la sesión.', [
                'user_id' => Auth::id(),
                'ip' => $request->ip()
            ]);
            return response()->json(['error' => 'No hay perfil activo.'], 401);
        }

        // --- INICIO DE LA DEPURACIÓN DE VALIDACIÓN ---
        try {
           
            Log::info('PlaybackController: Datos recibidos:', $request->all());

            $data = $request->validate([
                'pelicula_id' => 'required|integer|exists:peliculas,id',
            ]);

         
            Log::info('PlaybackController: Validación exitosa.');

        } catch (ValidationException $e) {
       
            Log::error('PlaybackController: ¡¡FALLO DE VALIDACIÓN!!', [
                'errores' => $e->errors(),
                'datos_recibidos' => $request->all()
            ]);
            return response()->json(['error' => 'Datos inválidos.'], 422);
        }
        // --- FIN DE LA DEPURACIÓN DE VALIDACIÓN ---

        try {
            // Intenta guardar en MongoDB

            // Obtener el nombre de la película
            $pelicula = \App\Models\Peliculas::find($data['pelicula_id']);
            $nombrePelicula = $pelicula ? $pelicula->title : null;

            HistorialVista::updateOrCreate(
                ['perfil_id' => $perfilId, 'pelicula_id' => $data['pelicula_id']],
                ['nombre_pelicula' => $nombrePelicula]
            );

           
            Log::info('PlaybackController: ¡Éxito! Historial guardado en MongoDB.');

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            // Captura cualquier error de MongoDB
            Log::critical('PlaybackController: ¡¡ERROR DE MONGODB!!', [
                'mensaje' => $e->getMessage(),
                'perfil_id' => $perfilId,
                'pelicula_id' => $data['pelicula_id']
            ]);
            return response()->json(['error' => 'Error al guardar en base de datos.'], 500);
        }
    }
}