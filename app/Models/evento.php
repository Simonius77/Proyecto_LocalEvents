<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

// Este es el modelo que gestiona la informacion de los eventos
class Evento extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    // Nombre de la tabla en la base de datos
    protected $table = 'eventos';
    
    // Identificador unico del evento
    protected $primaryKey = 'id_evento';

    // Campos que dejo que se guarden de forma automatica
    protected $fillable = [
        'nombre',
        'descripcion',
        'localizacion',
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
     * Saca la categoria a la que pertenece este evento
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    /**
     * Busca al usuario que ha organizado este evento
     */
    public function organizador()
    {
        return $this->belongsTo(User::class, 'id_organizador', 'id_usuario');
    }

    /**
     * Trae todas las reservas que se han hecho para este evento
     */
    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_evento', 'id_evento');
    }
}
