<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Soy el modelo que representa una reserva de un usuario en un evento
class Reserva extends Model
{
    // Tabla donde guardo las reservas
    protected $table = 'reservas';
    
    // El id de la reserva es mi llave principal
    protected $primaryKey = 'id_reserva';

    // Estos campos los puedo llenar todos de una vez
    protected $fillable = [
        'id_usuario',
        'id_evento',
        'cantidad',
        'total',
        'estado'
    ];

    /**
     * Saco los datos del usuario que hizo esta reserva
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    /**
     * Saco los datos del evento que se ha reservado
     */
    public function evento()
    {
        return $this->belongsTo(Evento::class, 'id_evento');
    }
}
