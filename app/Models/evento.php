<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

// Modelo Evento que representa la tabla eventos en la base de datos
// Implementa HasMedia para permitir la gestion de imagenes con Spatie MediaLibrary
class evento extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'eventos';
    protected $primaryKey = 'id_evento';

    // Campos que se pueden llenar de forma masiva
    protected $fillable = [
        'nombre',
        'descripcion',
        'latitud',
        'longitud',
        'precio',
        'aforo',
        'limite_edad',
        'fecha_inicio',
        'fecha_fin',
        'id_categoria',
        'id_organizador',
    ];

    /**
     * Obtener la categoria del evento.
     * Relacion muchos a uno con el modelo categoria.
     */
    public function categoria()
    {
        return $this->belongsTo(categoria::class, 'id_categoria', 'id_categoria');
    }

    /**
     * Obtener el organizador del evento (usuario).
     * Relacion muchos a uno con el modelo User.
     */
    public function organizador()
    {
        return $this->belongsTo(User::class, 'id_organizador', 'id_usuario');
    }

    /**
     * Obtener las reservas del evento.
     * Relacion uno a muchos con el modelo reserva.
     */
    public function reservas()
    {
        return $this->hasMany(reserva::class, 'id_evento', 'id_evento');
    }
}
