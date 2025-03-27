<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlaylistUser extends Model
{
    // Especifica el nombre de la tabla (si no sigue las convenciones de nombres de Laravel)
    protected $table = 'playlist_restricted_user';

    // Desactiva las marcas de tiempo si no están presentes en la tabla
    public $timestamps = true; // Cambia a false si no usas created_at y updated_at

    // Define los campos que se pueden llenar masivamente
    protected $fillable = [
        'playlist_id',
        'restricted_user_id',
    ];

    // Relación con el modelo Playlist
    public function playlist()
    {
        return $this->belongsTo(Playlist::class, 'playlist_id');
    }

    // Relación con el modelo RestrictedUser
    public function restrictedUser()
    {
        return $this->belongsTo(RestrictedUser::class, 'restricted_user_id');
    }
}