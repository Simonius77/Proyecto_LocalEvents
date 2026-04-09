<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Me encargo de validar los datos cuando se intenta actualizar un pago
class UpdatePagosRequest extends FormRequest
{
    /**
     * Compruebo si el usuario tiene permiso para hacer esta peticion
     */
    public function authorize(): bool
    {
        // De momento dejo que todos puedan, la seguridad va en el controlador
        return true;
    }

    /**
     * Defino las reglas que deben cumplir los datos del pago
     */
    public function rules(): array
    {
        return [
            // El id de la reserva debe existir en nuestra base de datos
            'id_reserva' => 'sometimes|required|exists:reservas,id_reserva',
            // El monto debe ser un numero y no puede ser negativo
            'monto' => 'sometimes|required|numeric|min:0',
            // La fecha de pago debe ser una fecha valida
            'fecha_pago' => 'sometimes|required|date',
            // El estado no puede ser demasiado largo
            'estado' => 'sometimes|required|string|max:50',
        ];
    }
}
