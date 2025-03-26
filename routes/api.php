<?php
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RestrictedUserController;

// Ruta registro
Route::post('register', [RegisterController::class, "register"]);

// Ruta login
Route::post('login', [LoginController::class, "login"]);

//************************* RUTAS DE REGISTRO Y LOGIN ***********************

// Ruta registro
Route::post('register', [
    RegisterController::class,
    "register"
]);

// Ruta login
Route::post('login', [LoginController::class, 'login'])->name('login');


//************************* RUTAS DE VIDEO ***********************************
// Ruta para crear un video dentro de una playlist
Route::middleware('auth:sanctum')->post('/playlists/{playlist_id}/videos', [VideoController::class, 'store']);

// Otras rutas de videos (con autenticación)
Route::middleware('auth:sanctum')->get('playlists/{playlist_id}/videos', [VideoController::class, 'index']);
Route::middleware('auth:sanctum')->get('playlists/{playlist_id}/videos/{video_id}', [VideoController::class, 'show']);
Route::middleware('auth:sanctum')->put('playlists/{playlist_id}/videos/{video_id}', [VideoController::class, 'update']);
Route::middleware('auth:sanctum')->delete('playlists/{playlist_id}/videos/{video_id}', [VideoController::class, 'destroy']);

//************************* RUTAS DE PLAYLIST *******************************

// Ruta para crear una playlist
Route::middleware('auth:sanctum')->post('/playlists', [PlaylistController::class, 'store']);

// Obtener todas las Playlists
Route::middleware('auth:sanctum')->get('/playlists', [PlaylistController::class, 'index']);

// Obtener una Playlist específica
Route::middleware('auth:sanctum')->get('/playlists/{id}', [PlaylistController::class, 'show']);

// Actualizar una Playlist
Route::middleware('auth:sanctum')->put('/playlists/{id}', [PlaylistController::class, 'update']);

// Eliminar una Playlist
Route::middleware('auth:sanctum')->delete('/playlists/{id}', [PlaylistController::class, 'destroy']);

//************************* RUTAS DE USUARIO Y VALIDACIÓN DE PIN ****************

// Obtener usuario y usuarios restringidos
Route::middleware('auth:sanctum')->get('/user', [HomeController::class, 'index']);

// Validar PIN del administrador
Route::middleware('auth:sanctum')->post('/validateAdminPin', [HomeController::class, 'validateAdminPin']);

// Validar PIN del usuario restringido
Route::middleware('auth:sanctum')->post('/validateUserPin/{id}', [HomeController::class, 'validateRestrictedUserPin']);


//************************* Rutas de gestión de usuarios restringidos ****************

// Obtener todos los usuarios restringidos
Route::middleware('auth:sanctum')->get('/restrictedUsers', [RestrictedUserController::class, 'index']);

// Obtener un usuario restringido por ID
Route::middleware('auth:sanctum')->get('/restrictedUsers/{id}', [RestrictedUserController::class, 'show']);

// Crear un nuevo usuario restringido
Route::middleware('auth:sanctum')->post('/CreaterestrictedUsers', [RestrictedUserController::class, 'store']);

// Actualizar un usuario restringido
Route::middleware('auth:sanctum')->post('/updateRestrictedUsers/{id}', [RestrictedUserController::class, 'update']);

// Eliminar un usuario restringido
Route::middleware('auth:sanctum')->delete('/DeleteUserRestricted/{id}', [RestrictedUserController::class, 'destroy']);
