<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\categoria;

// Controlador para gestionar las categorias de eventos a traves de la API
class CategoryController extends Controller
{
    /**
     * Muestra una lista de categorias con soporte para busqueda y ordenacion.
     * Retorna una coleccion paginada de recursos de categoria.
     */
    public function index()
    {
        // Define la columna por la cual se ordenaran los resultados
        $orderColumn = request('order_column', 'created_at');
        if (!in_array($orderColumn, ['id_categoria', 'nombre', 'created_at'])) {
            $orderColumn = 'created_at';
        }

        // Define la direccion de la ordenacion (asc o desc)
        $orderDirection = request('order_direction', 'desc');
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        // Realiza la consulta aplicando filtros opcionales de busqueda
        $categories = categoria::
            when(request('search_id'), function ($query) {
                // Filtra por ID exacto de la categoria
                $query->where('id_categoria', request('search_id'));
            })
            ->when(request('search_title'), function ($query) {
                // Filtra por coincidencia parcial en el nombre
                $query->where('nombre', 'like', '%' . request('search_title') . '%');
            })
            ->when(request('search_global'), function ($query) {
                // Busqueda global en ID o nombre
                $query->where(function ($q) {
                    $q->where('id_categoria', request('search_global'))
                        ->orWhere('nombre', 'like', '%' . request('search_global') . '%');

                });
            })
            ->orderBy($orderColumn, $orderDirection)
            ->paginate(50);

        // Retorna los datos transformados por CategoryResource
        return CategoryResource::collection($categories);
    }

    /**
     * Crea y almacena una nueva categoria en la base de datos.
     */
    public function store(StoreCategoryRequest $request)
    {
        // Verifica los permisos del usuario para crear una categoria
        $this->authorize('category-create');

        // Crea el registro con los datos validados del request
        $category = categoria::create($request->validated());

        // Retorna la nueva categoria como un recurso
        return new CategoryResource($category);
    }

    /**
     * Muestra los detalles de una categoria especifica.
     */
    public function show(categoria $category)
    {
        // Verifica los permisos para editar (ver detalles para edicion)
        $this->authorize('category-edit');

        // Retorna el recurso de la categoria solicitada
        return new CategoryResource($category);
    }

    /**
     * Actualiza los datos de una categoria existente.
     */
    public function update(categoria $category, StoreCategoryRequest $request)
    {
        // Verifica los permisos para editar la categoria
        $this->authorize('category-edit');

        // Actualiza los campos con los datos validados
        $category->update($request->validated());

        // Retorna el recurso actualizado
        return new CategoryResource($category);
    }

    /**
     * Elimina una categoria de la base de datos de forma permanente.
     */
    public function destroy(categoria $category)
    {
        // Verifica los permisos para eliminar la categoria
        $this->authorize('category-delete');

        // Elimina el registro
        $category->delete();

        // Retorna una respuesta exitosa sin contenido (204)
        return response()->noContent();
    }

    /**
     * Obtiene una lista completa de todas las categorias sin paginacion.
     */
    public function getList()
    {
        // Retorna todas las categorias para selectores o listas simples
        return CategoryResource::collection(categoria::all());
    }
}
