<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_usuario' => 'required|exists:users,id_usuario',
            'id_evento' => 'required|exists:eventos,id_evento',
            'fecha_reserva' => 'required|date',
            'estado' => 'required|string|max:50',
        ];
    }
}
