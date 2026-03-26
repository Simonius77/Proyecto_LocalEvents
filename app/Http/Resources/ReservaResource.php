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
        // Añado estas dos variables para saber si el evento ha pasado de fecha o si ya no quedan plazas
        $evento_caducado = false;
        $aforo_disponible = true;
        
        // Si el evento esta cargado, hago las comprobaciones necesarias
        if ($this->relationLoaded('evento') && $this->evento) {
            // Compruebo si ya ha pasado la fecha de inicio del evento
            $evento_caducado = \Carbon\Carbon::parse($this->evento->fecha_inicio)->isPast();
            
            // Cuento cuantas plazas estan ya reservadas y pagadas
            $ocupadas = \App\Models\Reserva::where('id_evento', $this->evento->id_evento)
                                ->whereIn('estado', ['pagado', 'confirmado'])
                                ->sum('cantidad');
                                
            // Compruebo si el aforo que queda es suficiente para abonar la cantidad de esta reserva
            $aforo_disponible = ($this->evento->aforo - $ocupadas) >= $this->cantidad;
        }

        return [
            'id_reserva' => $this->id_reserva,
            'id_usuario' => $this->id_usuario,
            'id_evento' => $this->id_evento,
            'cantidad' => $this->cantidad,
            'total' => $this->total,
            'estado' => $this->estado,
            'fecha_reserva' => $this->fecha_reserva,
            'evento_caducado' => $evento_caducado,
            'aforo_disponible' => $aforo_disponible,
            'evento' => new EventoResource($this->whenLoaded('evento')),
            'usuario' => new UserResource($this->whenLoaded('usuario')),
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }
}
