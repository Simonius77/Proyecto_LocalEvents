<?php
//Atencion!!!
//Ojo con las variables, yo programo en castellano antiguo by Simon.

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $apellidos = explode(' ', $this->apellidos ?? '', 2);
        $surname1 = $apellidos[0] ?? '';
        $surname2 = $apellidos[1] ?? '';

        return [
            'id' => $this->id_usuario,
            'name' => $this->nombre,
            'nombre' => $this->nombre, // Mantener por compatibilidad si hace falta
            'surname1' => $surname1,
            'surname2' => $surname2,
            'apellidos' => $this->apellidos,
            'email' => $this->email,
            'rol' => $this->rol,
            'roles' => RoleResource::collection($this->roles),
            'avatar' => count($this->getMedia('*')) > 0 ? $this->getMedia('*')[0]->getUrl() : null,
            'created_at' => $this->created_at ? $this->created_at->toDateString() : null
        ];
    }
}
