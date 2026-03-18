<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Http\Resources\EventoResource;
use Illuminate\Http\Request;

<<<<<<< HEAD
class EventoController extends Controller
{
    public function index()
    {
        $orderColumn = request('order_column', 'fecha_inicio');

        if (!in_array($orderColumn, ['id_evento', 'nombre', 'fecha_inicio', 'precio'])) {
            $orderColumn = 'fecha_inicio';
=======
// Yo uso este controlador para que se puedan manejar los eventos desde fuera
class EventoController extends Controller
{
    // Yo listo los eventos y permito que se busquen por nombre o id
    public function index()
    {
        // Yo elijo como ordenar la lista, por defecto por fecha de creacion
        $orderColumn = request('order_column', 'created_at');
        if (!in_array($orderColumn, ['id_evento', 'nombre', 'created_at'])) {
            $orderColumn = 'created_at';
>>>>>>> 577b10f (Yo implemento las reservas y los comentarios sin acentos)
        }

        $orderDirection = request('order_direction', 'desc');

        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

<<<<<<< HEAD
        $eventos = Evento::with(['categoria', 'organizador'])
            ->when(request('search_id'), function ($query) {
                $query->where('id_evento', request('search_id'));
            })
            ->when(request('search_title'), function ($query) {
                $query->where('nombre', 'like', '%' . request('search_title') . '%');
            })
            ->when(request('search_categoria'), function ($query) {
                $query->where('id_categoria', request('search_categoria'));
            })
=======
        // Yo busco los eventos segun lo que el usuario escriba en el buscador
        $eventos = Evento::
            when(request('search_id'), function ($query) {
                $query->where('id_evento', request('search_id'));
            })
>>>>>>> 577b10f (Yo implemento las reservas y los comentarios sin acentos)
            ->when(request('search_global'), function ($query) {
                $query->where(function ($q) {
                    $q->where('id_evento', request('search_global'))
                      ->orWhere('nombre', 'like', '%' . request('search_global') . '%');
                });
            })
            ->orderBy($orderColumn, $orderDirection)
            ->paginate(20);

        return EventoResource::collection($eventos);
    }

<<<<<<< HEAD
    public function store(\App\Http\Requests\StoreEventoRequest $request)
=======
    // Yo guardo un evento nuevo y tambien me encargo de guardar la foto si hay
    public function store(StoreEventoRequest $request)
>>>>>>> 577b10f (Yo implemento las reservas y los comentarios sin acentos)
    {
        // Yo anoto quien es el que crea el evento usando su id de usuario
        $validatedData = $request->validated();
        $validatedData['id_organizador'] = \Illuminate\Support\Facades\Auth::id();

<<<<<<< HEAD
        // Crea el evento con los datos validados
        $evento = Evento::create($validatedData);
=======
        // Yo creo el registro en la base de datos
        $evento = evento::create($validatedData);
>>>>>>> 577b10f (Yo implemento las reservas y los comentarios sin acentos)

        // Si me mandan una imagen, yo la guardo en la carpeta de imagenes
        if ($request->hasFile('imagen')) {
            $evento->addMediaFromRequest('imagen')->toMediaCollection('imagenes_eventos');
        }

        return new EventoResource($evento->load(['categoria', 'organizador']));
    }

<<<<<<< HEAD
    public function show(Evento $evento)
=======
    // Yo enseño los datos de un solo evento
    public function show(evento $evento)
>>>>>>> 577b10f (Yo implemento las reservas y los comentarios sin acentos)
    {
        return new EventoResource(
            $evento->load(['categoria', 'organizador', 'reservas'])
        );
    }

<<<<<<< HEAD
    public function update(\App\Http\Requests\UpdateEventoRequest $request, Evento $evento)
    {
=======
    // Yo pongo al dia los datos de un evento cuando alguien los cambia
    public function update(evento $evento, UpdateEventoRequest $request)
    {
        // Yo actualizo el texto del evento
>>>>>>> 577b10f (Yo implemento las reservas y los comentarios sin acentos)
        $evento->update($request->validated());

        // Si me pasan una foto nueva, yo borro la vieja y pongo la de ahora
        if ($request->hasFile('imagen')) {
            $evento->media()->delete();
            $evento->addMediaFromRequest('imagen')->toMediaCollection('imagenes_eventos');
        }

        return new EventoResource(
            $evento->load(['categoria', 'organizador'])
        );
    }

<<<<<<< HEAD
    public function destroy(Evento $evento)
=======
    // Yo borro el evento si el usuario tiene permiso para hacerlo
    public function destroy(evento $evento)
>>>>>>> 577b10f (Yo implemento las reservas y los comentarios sin acentos)
    {
        $currentUserId = \Illuminate\Support\Facades\Auth::id();
        $userRole = \Illuminate\Support\Facades\Auth::user()->rol;

        // Si soy el jefe (admin), yo puedo borrar cualquier cosa
        if ($userRole === 'admin' || $userRole === 'administrador') {
            $evento->delete();
            return response()->noContent();
        }

        // Si no soy el dueno del evento, yo no dejo que se borre
        if ($currentUserId !== $evento->id_organizador) {
            return response()->json(['message' => 'No puedes borrar un evento que no es tuyo'], 403);
        }

        $evento->delete();

        return response()->noContent();
    }
<<<<<<< HEAD
=======

    // Yo traigo todos los eventos de golpe para la pagina principal
    public function getList()
    {
        return EventoResource::collection(evento::all());
    }
>>>>>>> 577b10f (Yo implemento las reservas y los comentarios sin acentos)
}
