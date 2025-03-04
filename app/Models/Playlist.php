<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    protected $fillable = [
        'name',
        'admin_id',
        'associated_profiles'
    ];
    protected $casts = [
        'associated_profiles' => 'array',
    ];
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function videos()
    {
        return Video::whereJsonContains('playlists', $this->id)->get();
    }
}
