<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestrictedUser extends Model
{
    protected $fillable = [
        'fullname',
        'pin',
        'avatar',
        'user_id' 
    ];
   /**
     * Get the user that owns the restricted user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get playlists where this restricted user is associated.
     */
    public function playlists()
    {
        return Playlist::whereJsonContains('associated_profiles', $this->id)->get();
    }
}
