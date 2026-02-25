<?php
//Atencion!!!
//Ojo con las variables, yo programo en castellano antiguo by Simon.

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReservaResource extends JsonResource
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
            'id_reserva' => $this->id_reserva,
            'id_usuario' => $this->id_usuario,
            'id_evento' => $this->id_evento,
            'fecha_reserva' => $this->fecha_reserva,
            'estado' => $this->estado,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }
}
