<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Es el modelo que guarda los pagos realizados por los usuarios
class Pagos extends Model
{
    use HasFactory;

    // Tabla de pagos en la base de datos
    protected $table = 'pagos';
    
    // Clave primaria de la tabla
    protected $primaryKey = 'id_pago';

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'id_reserva',
        'monto',
        'fecha_pago',
        'estado',
    ];

    /**
     * Relaciona este pago con la reserva a la que pertenece
     */
    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'id_reserva', 'id_reserva');
    }
}
