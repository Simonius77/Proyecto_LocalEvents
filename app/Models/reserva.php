<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Defino este modelo como la representacion de una reserva en la base de datos
class Reserva extends Model
{
    // Indico que la tabla se llama reservas
    protected $table = 'reservas';
    // Marco el id_reserva como la clave primaria
    protected $primaryKey = 'id_reserva';

    // Aqui pongo los campos que permito que se llenen de forma masiva
    protected $fillable = [
        'id_usuario',
        'id_evento',
        'cantidad',
        'total',
        'estado'
    ];

    // Conecto esta reserva con el usuario que la hizo
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    // Conecto esta reserva con el evento al que pertenece
    public function evento()
    {
        return $this->belongsTo(Evento::class, 'id_evento');
    }
}
