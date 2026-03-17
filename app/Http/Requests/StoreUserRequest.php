<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // El nombre es obligatorio (aceptamos 'nombre' o 'name')
            'nombre' => ['required_without:name', 'nullable', 'string', 'max:255'],
            'name' => ['required_without:nombre', 'nullable', 'string', 'max:255'],
            
            // Los apellidos son opcionales (aceptamos 'apellidos', 'surname1' o 'surname2')
            'apellidos' => ['nullable', 'string', 'max:255'],
            'surname1' => ['nullable', 'string', 'max:255'],
            'surname2' => ['nullable', 'string', 'max:255'],

            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:usuarios'],
            'password' => ['required', 'string', 'min:8'],
            
            // El rol interno de la tabla usuarios
            'rol' => ['sometimes', 'string', 'in:usuario,organizador,administrador,admin'],
            
            // IDs de los roles de Spatie (permitimos array para el MultiSelect del admin)
            'role_id' => ['nullable', 'array'],
            'role_id.*' => ['exists:roles,id'],
        ];
    }
}
