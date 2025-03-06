<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'name',
        'url',
        'description',
        'user_id'
    ];

    /**
     * Relación: Un video pertenece a varias playlists.
     */
    public function playlists()
    {
        return $this->belongsToMany(Playlist::class, 'playlist_video', 'video_id', 'playlist_id');
    }
}


