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
            'name' => 'required|string|max:255',
            'admin_id' => 'required|exists:users,id',
            'associated_profiles' => 'nullable|array',
            'associated_profiles.*' => 'exists:users,id',
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
            'name' => 'required|string|max:255',
            'admin_id' => 'required|exists:users,id',
            'associated_profiles' => 'nullable|array',
            'associated_profiles.*' => 'exists:users,id'
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
}
