<?php
namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function store($playlist_id, Request $request)
    {
        // Validación de los datos de entrada
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'description' => 'nullable|string',
        ]);


        $playlist = Playlist::findOrFail($playlist_id);

        $video = new Video();
        $video->name = $request->input('name');
        $video->url = $request->input('url');
        $video->description = $request->input('description');
        $video->user_id = $request->input('user_id');
        $video->save();

        // Relacionar el video con la playlist
        $video->playlists()->attach($playlist->id);

        return response()->json($video, 201);
    }

    public function index($playlist_id)
    {
        $playlist = Playlist::findOrFail($playlist_id);
        $videos = $playlist->videos; // Relación definida en el modelo Playlist

        return response()->json($videos);
    }

    public function show($playlist_id, $video_id)
    {
        $playlist = Playlist::findOrFail($playlist_id);
        $video = $playlist->videos()->findOrFail($video_id);

        return response()->json($video);
    }

    public function update($playlist_id, $video_id, Request $request)
    {
        $playlist = Playlist::findOrFail($playlist_id);
        $video = $playlist->videos()->findOrFail($video_id);

        $video->update($request->only(['name', 'url', 'description']));

        return response()->json($video);
    }

    public function destroy($playlist_id, $video_id)
    {
        $playlist = Playlist::findOrFail($playlist_id);
        $video = $playlist->videos()->findOrFail($video_id);

        $video->delete();

        return response()->json(null, 204);
    }
}
