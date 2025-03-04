<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Video extends Model
{
    protected $fillable = 
    [
    'name', 
    'url', 
    'description',
    'playlists',
    'user_id'
    ];

    /**
     * Relación: Un video pertenece a una playlist.
     */
    public function playlist(): BelongsTo
    {
        return $this->belongsTo(Playlist::class);
    }
}

