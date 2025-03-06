<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    use HasFactory;

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'name',
        'admin_id',
        'associated_profiles',
    ];

    // Relación con el modelo User (admin)
    public function admin()
    {
        return $this->belongsTo(User::class);
    }

    public function videos()
    {
    return $this->hasMany(Video::class);
    }

}

