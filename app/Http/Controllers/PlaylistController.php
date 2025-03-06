<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    // Mostrar todas las playlists
    public function index()
    {
        $playlists = Playlist::all();
        return response()->json($playlists);
    }

    // Mostrar una playlist por ID
    public function show($id)
    {
        $playlist = Playlist::find($id);

        if (!$playlist) {
            return response()->json(['message' => 'Playlist no encontrada'], 404);
        }

        return response()->json($playlist);
    }

    // Crear una nueva playlist
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'admin_id' => 'required|exists:users,id',
            'associated_profiles' => 'nullable|array',
        ]);

        $playlist = Playlist::create([
            'name' => $request->name,
            'admin_id' => $request->admin_id,
            'associated_profiles' => json_encode($request->associated_profiles),
        ]);

        return response()->json($playlist, 201);
    }

    // Actualizar una playlist existente
    public function update(Request $request, $id)
    {
        $playlist = Playlist::find($id);

        if (!$playlist) {
            return response()->json(['message' => 'Playlist no encontrada'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'admin_id' => 'required|exists:users,id',
            'associated_profiles' => 'nullable|array',
        ]);

        $playlist->update([
            'name' => $request->name,
            'admin_id' => $request->admin_id,
            'associated_profiles' => json_encode($request->associated_profiles),
        ]);

        return response()->json($playlist);
    }

    // Eliminar una playlist
    public function destroy($id)
    {
        $playlist = Playlist::find($id);

        if (!$playlist) {
            return response()->json(['message' => 'Playlist no encontrada'], 404);
        }

        $playlist->delete();
        return response()->json(['message' => 'Playlist eliminada con éxito']);
    }
}
