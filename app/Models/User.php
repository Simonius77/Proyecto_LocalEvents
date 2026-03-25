<?php

namespace App\Models;

use App\Notifications\UserResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

// Soy el modelo que representa a los usuarios del sistema
class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, InteractsWithMedia;

    // Indico que los datos se guardan en la tabla usuarios
    protected $table = 'usuarios';
    // Marco id_usuario como la llave principal
    protected $primaryKey = 'id_usuario';

    // Permito que estos campos se puedan rellenar de golpe
    protected $fillable = [
        'nombre',
        'apellidos',
        'telefono',
        'email',
        'password',
        'latitud',
        'longitud',
        'fecha_nacimiento',
        'rol',
        'activo'
    ];

    // Escondo la contraseña y el token para que no se vean por ahi
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Me encargo de que la fecha de verificacion se maneje como tiempo real
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Mando el aviso por correo cuando alguien olvida su contraseña
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new UserResetPasswordNotification($token));
    }

    // Preparo el sitio donde se guardan las fotos de los usuarios
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images/users')
            ->useFallbackUrl('/images/placeholder.jpg')
            ->useFallbackPath(public_path('/images/placeholder.jpg'));
    }

    // Cambio el tamaño de las fotos si hace falta para que no pesen tanto
    public function registerMediaConversions(Media $media = null): void
    {
        if (env('RESIZE_IMAGE') === true) {
            $this->addMediaConversion('resized-image')
                ->width(env('IMAGE_WIDTH', 300))
                ->height(env('IMAGE_HEIGHT', 300));
        }
    }

    // Conecto al usuario con los eventos que el mismo organiza
    public function eventos()
    {
        return $this->hasMany(evento::class, 'id_organizador', 'id_usuario');
    }

    // Conecto al usuario con todas las reservas que ha hecho
    public function reservas()
    {
        return $this->hasMany(reserva::class, 'id_usuario', 'id_usuario');
    }
}
