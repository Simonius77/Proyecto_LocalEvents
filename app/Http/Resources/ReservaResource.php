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
            'cantidad' => $this->cantidad,
            'total' => $this->total,
            'estado' => $this->estado,
            'fecha_reserva' => $this->fecha_reserva,
            'evento' => new EventoResource($this->whenLoaded('evento')),
            'usuario' => new UserResource($this->whenLoaded('usuario')),
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }
}
