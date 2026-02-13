<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class reserva extends Model
{
    protected $table = 'reservas';
    protected $primaryKey = 'id_reserva';

    protected $fillable = [
        'id_usuario',
        'id_evento',
        'fecha_reserva',
        'estado',
    ];

    /**
     * Obtener el usuario que hizo la reserva.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Obtener el evento reservado.
     */
    public function evento()
    {
        return $this->belongsTo(evento::class, 'id_evento', 'id_evento');
    }

    /**
     * Obtener los pagos asociados a la reserva.
     */
    public function pagos()
    {
        return $this->hasMany(pagos::class, 'id_reserva', 'id_reserva');
    }
}
