<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ReservaResource;

// Manejo todas las peticiones que tienen que ver con las reservas de eventos
class ReservaController extends Controller
{
    // Aqui listo las reservas. Si soy admin veo todas, si no solo las micas
    public function index()
    {
        $user = Auth::user();
        if ($user->rol === 'administrador') {
            return ReservaResource::collection(
                Reserva::with(['usuario', 'evento'])->latest()->get()
            );
        }
        
        return ReservaResource::collection(
            Reserva::with('evento')
                ->where('id_usuario', $user->id_usuario)
                ->latest()
                ->get()
        );
    }

    // Guardo una nueva reserva en la base de datos con los datos que me mandan
    public function store(Request $request)
    {
        $request->validate([
            'id_evento' => 'required|exists:eventos,id_evento',
            'cantidad' => 'required|integer|min:1'
        ]);

        $evento = Evento::findOrFail($request->id_evento);
        $total = $evento->precio * $request->cantidad;
        
        $reserva = Reserva::create([
            'id_usuario' => Auth::id(),
            'id_evento' => $request->id_evento,
            'cantidad' => $request->cantidad,
            'total' => $total,
            'estado' => ($total > 0) ? 'pendiente' : 'confirmado'
        ]);

        return new ReservaResource($reserva->load('evento'));
    }

    // Marco una reserva como pagada cuando el usuario pulsa el boton
    public function pagar($id)
    {
        $reserva = Reserva::where('id_usuario', Auth::id())->findOrFail($id);
        $reserva->update(['estado' => 'pagado']);

        return new ReservaResource($reserva->load('evento'));
    }

    // Aqui anoto que el usuario quiere cancelar su reserva para que lo vea el jefe
    public function solicitarCancelacion($id)
    {
        $reserva = Reserva::where('id_usuario', Auth::id())->findOrFail($id);
        $reserva->update(['estado' => 'solicitada_cancelacion']);

        return new ReservaResource($reserva->load('evento'));
    }

    // Muestro los detalles de una reserva suelta
    public function show($id)
    {
        $reserva = Reserva::with(['usuario', 'evento'])->findOrFail($id);
        return response()->json(['data' => $reserva]);
    }

    // Borro la reserva si el que lo pide es el dueno o el admin
    public function destroy($id)
    {
        $user = Auth::user();
        $reserva = Reserva::findOrFail($id);
        
        if ($user->rol === 'administrador' || $reserva->id_usuario === $user->id_usuario) {
            $reserva->delete();
            return response()->json(['message' => 'Reserva eliminada']);
        }

        return response()->json(['message' => 'No autorizado'], 403);
    }

    // Obtengo todas las reservas que los usuarios han pedido cancelar
    public function getCancelacionesPendientes()
    {
        $user = Auth::user();
        
        // Si el usuario revisando es el administrador le descargo todas las de la plataforma entera
        if ($user->rol === 'administrador') {
            return ReservaResource::collection(
                Reserva::with(['usuario', 'evento'])
                    ->where('estado', 'solicitada_cancelacion')
                    ->latest()
                    ->get()
            );
        }
        
        // Si el usuario es organizador busco unicamente las cancelaciones de sus eventos
        return ReservaResource::collection(
            Reserva::with(['usuario', 'evento'])
                ->whereHas('evento', function ($query) use ($user) {
                    $query->where('id_organizador', $user->id_usuario);
                })
                ->where('estado', 'solicitada_cancelacion')
                ->latest()
                ->get()
        );
    }

    // Confirmo la cancelacion de una reserva para liberar el aforo y marcarla como cancelada oficialmente
    public function confirmarCancelacion($id)
    {
        $user = Auth::user();
        $reserva = Reserva::with('evento')->findOrFail($id);
        
        // Compruebo que quien intenta cancelar es el admin general o el dueño legitimo de la publicacion del evento
        if ($user->rol === 'administrador' || $reserva->evento->id_organizador === $user->id_usuario) {
            $reserva->update(['estado' => 'cancelado']);
            return new ReservaResource($reserva);
        }
        
        return response()->json(['message' => 'No autorizado para cancelar'], 403);
    }
}
