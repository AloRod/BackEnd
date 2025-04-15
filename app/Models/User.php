<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use App\Notifications\VerifyEmailNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, MustVerifyEmailTrait;

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

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
        ];
    }

    public function restrictedUsers()
    {
        return $this->hasMany(RestrictedUser::class);
    }

    public function playlists()
    {
        return $this->hasMany(Playlist::class, 'admin_id');
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }
    // notifications correo de verificación. 
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification());
    }
}
