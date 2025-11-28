<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\PeliculasApiController;
use App\Http\Controllers\FavoritoController; // ⭐ NUEVO
use App\Models\User;

// Rutas de autenticación (públicas)
Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login', [AuthApiController::class, 'login']);

// Rutas protegidas (requieren autenticación)
// Rutas protegidas (requieren autenticación)

Route::middleware('auth:sanctum')->group(function () {
    // Usuario autenticado
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });
    // ⭐ NUEVAS RUTAS DE FAVORITOS
    // IMPORTANTE: Las rutas específicas (check) DEBEN ir antes de las rutas con parámetros genéricos
    Route::get('/favoritos/check/{peliculaId}', [FavoritoController::class, 'check']);
    Route::get('/favoritos', [FavoritoController::class, 'index']);
    Route::post('/favoritos/{peliculaId}', [FavoritoController::class, 'store']);
    Route::delete('/favoritos/{peliculaId}', [FavoritoController::class, 'destroy']);

});

// Rutas de usuarios (públicas para este ejemplo, pero deberían estar protegidas)
Route::get('/users', function () {
    return response()->json(User::all());
});

Route::get('/users/count', function () {
    return response()->json(['count' => User::count()]);
});

Route::get('/users/{id}', function ($id) {
    $user = User::find($id);
    if ($user) {
        return response()->json($user);
    }
    return response()->json(['error' => 'Usuario no encontrado'], 404);
});

// Endpoints para películas (públicos)
Route::get('/peliculas', [PeliculasApiController::class, 'index']);
Route::get('/peliculas/{id}', [PeliculasApiController::class, 'show']);
