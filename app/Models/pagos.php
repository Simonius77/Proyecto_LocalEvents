<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Soy el modelo que guarda los pagos realizados por los usuarios
class Pagos extends Model
{
    use HasFactory;

    // Tabla de pagos en mi base de datos
    protected $table = 'pagos';
    
    // Llave primaria de la tabla
    protected $primaryKey = 'id_pago';

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'id_reserva',
        'monto',
        'fecha_pago',
        'estado',
    ];

    /**
     * Relaciono este pago con la reserva a la que pertenece
     */
    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'id_reserva', 'id_reserva');
    }
}
