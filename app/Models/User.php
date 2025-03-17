<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens; 

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'email',
        'password',
        'phone',
        'pin',
        'name',
        'lastname',
        'country',
        'birthdate',
        'status',
        'verification_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
        ];
    }

    /**
     * Relación con el usuario principal (para cuentas restringidas).
     */
   /**
     * Get the restricted users associated with the user.
     */
    public function restrictedUsers()
    {
        return $this->hasMany(RestrictedUser::class);
    }

    /**
     * Get the playlists administered by the user.
     */
    public function playlists()
    {
        return $this->hasMany(Playlist::class, 'admin_id');
    }

    /**
     * Get the videos that belong to the user.
     */
    public function videos()
    {
        return $this->hasMany(Video::class);
    }
}