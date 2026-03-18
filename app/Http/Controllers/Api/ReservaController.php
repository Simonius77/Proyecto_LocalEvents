<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Yo manejo todas las peticiones que tienen que ver con las reservas de eventos
class ReservaController extends Controller
{
    // Aqui listo las reservas. Si soy admin veo todas, si no solo las micas
    public function index()
    {
        $user = Auth::user();
        if ($user->rol === 'administrador') {
            return response()->json([
                'data' => Reserva::with(['usuario', 'evento'])->latest()->get()
            ]);
        }
        
        return response()->json([
            'data' => Reserva::with('evento')
                ->where('id_usuario', $user->id_usuario)
                ->latest()
                ->get()
        ]);
    }

    // Yo guardo una nueva reserva en la base de datos con los datos que me mandan
    public function store(Request $request)
    {
        $request->validate([
            'id_evento' => 'required|exists:eventos,id_evento',
            'cantidad' => 'required|integer|min:1'
        ]);

        $evento = Evento::findOrFail($request->id_evento);
        
        $reserva = Reserva::create([
            'id_usuario' => Auth::id(),
            'id_evento' => $request->id_evento,
            'cantidad' => $request->cantidad,
            'total' => $evento->precio * $request->cantidad,
            'estado' => 'pendiente'
        ]);

        return response()->json([
            'message' => 'Reserva realizada con exito',
            'data' => $reserva->load('evento')
        ], 201);
    }

    // Yo marco una reserva como pagada cuando el usuario pulsa el boton
    public function pagar($id)
    {
        $reserva = Reserva::where('id_usuario', Auth::id())->findOrFail($id);
        $reserva->update(['estado' => 'pagado']);

        return response()->json([
            'message' => 'Reserva pagada correctamente',
            'data' => $reserva
        ]);
    }

    // Aqui anoto que el usuario quiere cancelar su reserva para que lo vea el jefe
    public function solicitarCancelacion($id)
    {
        $reserva = Reserva::where('id_usuario', Auth::id())->findOrFail($id);
        $reserva->update(['estado' => 'solicitada_cancelacion']);

        return response()->json([
            'message' => 'Cancelacion solicitada al administrador y organizador',
            'data' => $reserva
        ]);
    }

    // Muestro los detalles de una reserva suelta
    public function show($id)
    {
        $reserva = Reserva::with(['usuario', 'evento'])->findOrFail($id);
        return response()->json(['data' => $reserva]);
    }

    // Yo borro la reserva si el que lo pide es el dueno o el admin
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
}
