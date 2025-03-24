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

    // Relación con Playlists (muchos a muchos)
    public function playlists()
    {
        return $this->belongsToMany(Playlist::class)->withTimestamps();
    }
}

