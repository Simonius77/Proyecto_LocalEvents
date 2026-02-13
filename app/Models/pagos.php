<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class pagos extends Model
{
    protected $table = 'pagos';
    protected $primaryKey = 'id_pago';

    protected $fillable = [
        'id_reserva',
        'monto',
        'fecha_pago',
        'estado',
    ];

    /**
     * Obtener reserva asociada al pago
     * 
     */
    public function reserva()
    {
        return $this->belongsTo(reserva::class, 'id_reserva', 'id_reserva');
    }
}
