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
            'id' => $this->id_pago,
            'reservation_id' => $this->id_reserva,
            'amount' => $this->monto,
            'payment_date' => $this->fecha_pago,
            'status' => $this->estado,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }
}
