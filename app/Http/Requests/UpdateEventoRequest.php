<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Buscamos el evento en la ruta
        $evento = $this->route('evento');
        
        // Si lo que llega es solo el ID (un numero), lo buscamos en la base de datos
        if (is_numeric($evento)) {
            $evento = \App\Models\Evento::find($evento);
        }

        // Si no encontramos el evento, no dejamos pasar
        if (!$evento) {
            return false;
        }

        // Sacamos el usuario que esta usando la app
        $user = $this->user();

        // Si el usuario es administrador, le dejamos editar todo
        if ($user->rol === 'admin') {
            return true;
        }
        
        // Si es un organizador normal, solo le dejamos editar si el es el dueño
        // Comparamos los IDs de usuario y organizador
        return $user->id_usuario === $evento->id_organizador;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => 'sometimes|required|string|max:255',
            'descripcion' => 'nullable|string',
            'localizacion' => 'sometimes|required|string|max:255',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'precio' => 'sometimes|required|numeric|min:0',
            'aforo' => 'sometimes|required|integer|min:1',
            // Corregido limite_edad para que acepte los valores del enum
            'limite_edad' => 'nullable|string|in:+18,todas',
            'fecha_inicio' => 'sometimes|required|date',
            'fecha_fin' => 'sometimes|required|date|after_or_equal:fecha_inicio',
            'id_categoria' => 'sometimes|required|exists:categorias,id_categoria',
            'id_organizador' => 'sometimes|required|exists:usuarios,id_usuario',
            // Nueva regla para la imagen del evento
            'imagen' => 'nullable|image|max:2048',
        ];
    }
}
