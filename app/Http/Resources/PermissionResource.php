<?php
//Atencion!!!
//Ojo con las variables, yo programo en castellano antiguo by Simon.

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id_permiso' => $this->id,
            'nombre' => $this->name,
            'guardia' => $this->guard_name,
            'created_at' => $this->created_at?->toDateString()
        ];
    }
}
