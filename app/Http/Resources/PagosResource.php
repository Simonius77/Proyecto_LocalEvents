<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PagosResource extends JsonResource
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
            'id_pago' => $this->id_pago,
            'id_reserva' => $this->id_reserva,
            'monto' => $this->monto,
            'fecha_pago' => $this->fecha_pago,
            'estado' => $this->estado,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }
}
