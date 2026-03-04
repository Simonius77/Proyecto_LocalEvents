<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_evento' => $this->id_evento,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'precio' => $this->precio,
            'aforo' => $this->aforo,
            'limite_edad' => $this->limite_edad,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'categoria' => $this->whenLoaded('categoria'),
            'organizador' => $this->whenLoaded('organizador'),
        ];
    }
}
