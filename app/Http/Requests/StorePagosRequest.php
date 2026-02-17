<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePagosRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id_reserva' => 'required|integer|exists:reservas,id_reserva',
            'monto' => 'required|numeric',
            'fecha_pago' => 'required|date',
            'estado' => 'required|string'
        ];
    }
}
