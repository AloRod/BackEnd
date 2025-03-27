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

    public function getAvatarUrlAttribute()
{
    if ($this->avatar) {
        return asset('storage/' . $this->avatar);
    }
    return null;
}
    /**
     * Get playlists where this restricted user is associated.
     */
    public function playlists()
    {
        return Playlist::whereJsonContains('associated_profiles', $this->id)->get();
    }
    
}
