<?php
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\PlaylistController;

//Ruta registro
Route::post('register', [
    RegisterController::class,
    "register"
]);

//Ruta login
Route::post('login', [
    LoginController::class,
    "login"
]);

//************************VIDEO******** */

Route::middleware('auth:sanctum')->post('playlists/{playlist_id}/videos', [VideoController::class, 'store']);
Route::middleware('auth:sanctum')->get('playlists/{playlist_id}/videos', [VideoController::class, 'index']);
Route::middleware('auth:sanctum')->get('playlists/{playlist_id}/videos/{video_id}', [VideoController::class, 'show']);
Route::middleware('auth:sanctum')->put('playlists/{playlist_id}/videos/{video_id}', [VideoController::class, 'update']);
Route::middleware('auth:sanctum')->delete('playlists/{playlist_id}/videos/{video_id}', [VideoController::class, 'destroy']);

//******************* PLAYLIST *****************

// RUta para crear una playlist
Route::post('/playlists', [PlaylistController::class, 'store']);

// Obtener todas las Playlists
Route::get('/playlists', [PlaylistController::class, 'index']);

// Obtener una Playlist específica
Route::get('/playlists/{id}', [PlaylistController::class, 'show']);

// Actualizar una Playlist
Route::put('/playlists/{id}', [PlaylistController::class, 'update']);

// Eliminar una Playlist
Route::delete('/playlists/{id}', [PlaylistController::class, 'destroy']);

