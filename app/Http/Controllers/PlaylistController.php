<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\Playlist;
use App\Models\PlaylistUser;
use Illuminate\Http\Request;
use App\Models\RestrictedUser;

class PlaylistController extends Controller
{
    // Display all playlists
    public function index()
    {
        $playlists = Playlist::all();
        return response()->json($playlists);
    }

    // Show a playlist by ID
    public function show($id)
    {
        $playlist = Playlist::find($id);

        if (!$playlist) {
            return response()->json(['message' => 'Playlist not found'], 404);
        }

        return response()->json($playlist);
    }

    // Create a new playlist
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3',
            'admin_id' => 'required|integer',
            'associated_profiles' => 'array',
        ]);

        $playlist = Playlist::create([
            'name' => $request->name,
            'admin_id' => $request->admin_id,
            'associated_profiles' => json_encode($request->associated_profiles),
        ]);

        if ($request->has('associated_profiles') && is_array($request->associated_profiles)) {

            foreach ($request->associated_profiles as $profileId) {
                PlaylistUser::create([
                    'playlist_id' => $playlist->id,
                    'restricted_user_id' => $profileId,
                ]);
            }
        }

        return response()->json($playlist, 201);
    }

    // Update an existing playlist
    public function update(Request $request, $id)
    {
        $playlist = Playlist::find($id);

        if (!$playlist) {
            return response()->json(['message' => 'Playlist not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|min:3',
            'description' => 'nullable|string',
            'user_id' => 'required|integer',
            'admin_id' => 'required|integer',
            'associated_profiles' => 'array',
        ]);

        // Actualizar la playlist
        $playlist->update([
            'name' => $request->name,
            'admin_id' => $request->admin_id,
            'associated_profiles' => json_encode($request->associated_profiles),
        ]);

        // Eliminar todas las relaciones existentes para esta playlist
        PlaylistUser::where('playlist_id', $playlist->id)->delete();

        // Si hay perfiles asociados, crear las nuevas relaciones en PlaylistUser
        if ($request->has('associated_profiles') && is_array($request->associated_profiles)) {
            foreach ($request->associated_profiles as $profileId) {
                PlaylistUser::create([
                    'playlist_id' => $playlist->id,
                    'restricted_user_id' => $profileId,
                ]);
            }
        }

        return response()->json($playlist);
    }

    // Delete a playlist
    public function destroy($id)
    {
        $playlist = Playlist::find($id);

        if (!$playlist) {
            return response()->json(['message' => 'Playlist not found'], 404);
        }

        $playlist->delete();
        return response()->json(['message' => 'Playlist successfully deleted']);
    }


    /*public function getUserPlaylists($id)
    {
   // Convertir el ID del usuario en un array JSON
   $userIdArray = json_encode([$id]);

   // Buscar playlists usando Eloquent y JSONB
   $playlists = Playlist::whereJsonContains('associated_profiles', [$id])->get();

   if ($playlists->isEmpty()) {
       return response()->json(['message' => 'No playlists found for this user'], 404);
   }

   return response()->json($playlists);
    } */

    public function getUserPlaylists(Request $request, $id)
    {
        // Buscar al usuario restringido por su ID y PIN
        $user = RestrictedUser::where('id', $id)
                            ->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 403);
        }

        // Obtener los IDs de las playlists asociadas al usuario mediante PlaylistUser
        $playlistIds = PlaylistUser::where('restricted_user_id', $user->id)
                                ->pluck('playlist_id')
                                ->toArray();

        // Obtener las playlists usando los IDs encontrados
        $playlists = Playlist::whereIn('id', $playlistIds)
                            ->withCount('videos') // Contar los videos asociados a cada playlist
                            ->get()
                            ->map(function ($playlist) {
                                return [
                                    'id' => $playlist->id,
                                    'name' => $playlist->name,
                                    'video_count' => $playlist->videos_count,
                                ];
                            });

        return response()->json([
            'message' => 'Access granted',
            'playlists' => $playlists,
        ], 200);
    }
}
