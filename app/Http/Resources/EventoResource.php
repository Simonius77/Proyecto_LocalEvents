<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_evento' => $this->id_evento,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'latitud' => $this->latitud,
            'longitud' => $this->longitud,
            'precio' => $this->precio,
            'aforo' => $this->aforo,
            'limite_edad' => $this->limite_edad,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'id_categoria' => $this->id_categoria,
            'id_organizador' => $this->id_organizador,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
