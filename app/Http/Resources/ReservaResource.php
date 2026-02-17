<?php

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
            'id' => $this->id_reserva,
            'user_id' => $this->id_usuario,
            'event_id' => $this->id_evento,
            'reservation_date' => $this->fecha_reserva,
            'status' => $this->estado,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }
}
