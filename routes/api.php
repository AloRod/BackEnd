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

// ************************ VIDEO ************************

Route::post('playlists/{playlist_id}/videos', [VideoController::class, 'store']);
Route::get('playlists/{playlist_id}/videos', [VideoController::class, 'index']);
Route::get('playlists/{playlist_id}/videos/{video_id}', [VideoController::class, 'show']);
Route::put('playlists/{playlist_id}/videos/{video_id}', [VideoController::class, 'update']);
Route::delete('playlists/{playlist_id}/videos/{video_id}', [VideoController::class, 'destroy']);

// ******************* PLAYLIST *****************

//************************* RUTAS DE REGISTRO Y LOGIN ***********************

// Ruta registro
Route::post('register', [
    RegisterController::class,
    "register"
]);

// Ruta login
Route::post('login', [LoginController::class, 'login'])->name('login');


//************************* RUTAS DE VIDEO ***********************************

Route::post('/playlists/{playlist_id}/videos', [VideoController::class, 'store']);
Route::middleware('auth:sanctum')->get('playlists/{playlist_id}/videos', [VideoController::class, 'index']);
Route::middleware('auth:sanctum')->get('playlists/{playlist_id}/videos/{video_id}', [VideoController::class, 'show']);
Route::middleware('auth:sanctum')->put('playlists/{playlist_id}/videos/{video_id}', [VideoController::class, 'update']);
Route::middleware('auth:sanctum')->delete('playlists/{playlist_id}/videos/{video_id}', [VideoController::class, 'destroy']);

//************************* RUTAS DE PLAYLIST *******************************


// Ruta para crear una playlist
Route::post('/playlists', [PlaylistController::class, 'store']);

// Obtener todas las Playlists
Route::get('/playlists', [PlaylistController::class, 'index']);

// Obtener una Playlist específica
Route::get('/playlists/{id}', [PlaylistController::class, 'show']);

// Actualizar una Playlist
Route::put('/playlists/{id}', [PlaylistController::class, 'update']);

// Eliminar una Playlist
Route::delete('/playlists/{id}', [PlaylistController::class, 'destroy']);


//************************* RUTAS DE USUARIO Y VALIDACIÓN DE PIN ****************

// Obtener usuario y usuarios restringidos
Route::middleware('auth:sanctum')->get('/user', [HomeController::class, 'index']);

// Validar PIN del administrador
Route::middleware('auth:sanctum')->post('/validateAdminPin', [HomeController::class, 'validateAdminPin']);

// Validar PIN del usuario restringido
Route::middleware('auth:sanctum')->post('/validate-user-pin/{id}', [HomeController::class, 'validateRestrictedUserPin']);


//************************* Rutas de gestión de usuarios restringidos ****************

// Obtener todos los usuarios restringidos
Route::middleware('auth:sanctum')->get('/restricted-users', [RestrictedUserController::class, 'index']);

// Crear un nuevo usuario restringido
Route::middleware('auth:sanctum')->post('/restricted-users', [RestrictedUserController::class, 'store']);

// Actualizar un usuario restringido
Route::middleware('auth:sanctum')->put('/restricted-users/{id}', [RestrictedUserController::class, 'update']);

// Eliminar un usuario restringido
Route::middleware('auth:sanctum')->delete('/restricted-users/{id}', [RestrictedUserController::class, 'destroy']);
 