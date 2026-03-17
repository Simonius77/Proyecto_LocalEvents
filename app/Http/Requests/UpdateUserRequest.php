<?php
//Atencion!!!
//Ojo con las variables, yo programo en castellano antiguo by Simon.

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request para validar la actualizacion de los datos de un usuario.
 */
class UpdateUserRequest extends FormRequest
{
    /**
     * Determina si el usuario esta autorizado para realizar esta peticion.
     * En este caso permitimos que cualquier usuario autenticado (o segun la logica de rutas) lo haga.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define las reglas de validacion que se aplicaran a la peticion de actualizacion.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // El nombre es obligatorio si se envia (aceptamos 'nombre' o 'name')
            'nombre' => ['required_without:name', 'nullable', 'string', 'max:255'],
            'name' => ['required_without:nombre', 'nullable', 'string', 'max:255'],
            
            // Los apellidos son opcionales (aceptamos 'apellidos', 'surname1' o 'surname2')
            'apellidos' => ['nullable', 'string', 'max:255'],
            'surname1' => ['nullable', 'string', 'max:255'],
            'surname2' => ['nullable', 'string', 'max:255'],

            // El telefono es opcional con limite de caracteres
            'telefono' => ['nullable', 'string', 'max:20'],

            // El email es obligatorio, debe ser valido y unico, ignorando el ID del usuario actual
            // Usamos $this->user->id_usuario porque es el nombre del ID en tu tabla
            'email' => 'required|string|email|max:255|unique:usuarios,email,' . ($this->user->id_usuario ?? $this->route('user')->id_usuario) . ',id_usuario',
            
            // Coordenadas geograficas opcionales
            'latitud' => ['nullable', 'numeric'],
            'longitud' => ['nullable', 'numeric'],
            
            // Fecha de nacimiento en formato fecha
            'fecha_nacimiento' => ['nullable', 'date'],
            
            // El rol debe ser uno de los permitidos si se intenta cambiar
            'rol' => ['sometimes', 'string', 'in:usuario,organizador,administrador,admin'],
            
            // Estado de actividad del usuario
            'activo' => ['sometimes', 'boolean'],
            
            // La contraseña es opcional al actualizar, minimo 8 caracteres si se proporciona
            'password' => ['nullable', 'string', 'min:8'],
            
            // IDs de los roles de Spatie (ahora permitimos que sea un array de IDs)
            'role_id' => ['nullable', 'array'],
            'role_id.*' => ['exists:roles,id'],
        ];
    }
}
