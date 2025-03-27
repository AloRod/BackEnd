<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use Illuminate\Http\Request;

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
    'description' => 'nullable|string',
    'user_id' => 'required|integer',
    'admin_id' => 'required|integer',
    'associated_profiles' => 'array',
        ]);

        $playlist = Playlist::create([
            'name' => $request->name,
            'admin_id' => $request->admin_id,
            'associated_profiles' => json_encode($request->associated_profiles),
        ]);

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

        $playlist->update([
            'name' => $request->name,
            'admin_id' => $request->admin_id,
            'associated_profiles' => json_encode($request->associated_profiles),
        ]);

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
    public function getUserPlaylists($id)
{
   // Convertir el ID del usuario en un array JSON
   $userIdArray = json_encode([$id]);

   // Buscar playlists usando Eloquent y JSONB
   $playlists = Playlist::whereJsonContains('associated_profiles', [$id])->get();

   if ($playlists->isEmpty()) {
       return response()->json(['message' => 'No playlists found for this user'], 404);
   }

   return response()->json($playlists);
}
}
