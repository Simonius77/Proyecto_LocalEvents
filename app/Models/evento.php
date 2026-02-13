<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class evento extends Model
{
    use HasFactory;

    protected $table = 'eventos';
    protected $primaryKey = 'id_evento';

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
     * Obtener la categoría del evento.
     */
    public function categoria()
    {
        return $this->belongsTo(categoria::class, 'id_categoria', 'id_categoria');
    }

    /**
     * Obtener el organizador del evento (usuario).
     */
    public function organizador()
    {
        return $this->belongsTo(User::class, 'id_organizador', 'id_usuario');
    }

    /**
     * Obtener las reservas del evento.
     */
    public function reservas()
    {
        return $this->hasMany(reserva::class, 'id_evento', 'id_evento');
    }
}
