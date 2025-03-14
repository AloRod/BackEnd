<?php
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\PlaylistController;

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
