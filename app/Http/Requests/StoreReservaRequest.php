<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservaRequest extends FormRequest
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
            'id_usuario' => 'required|integer|exists:usuarios,id_usuario',
            'id_evento' => 'required|integer|exists:eventos,id_evento',
            'fecha_reserva' => 'required|date',
            'estado' => 'nullable|string|in:reservada,cancelada'
        ];
    }
}
