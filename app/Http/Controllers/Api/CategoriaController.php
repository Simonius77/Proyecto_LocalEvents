<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoriaResource;
use App\Models\Categoria;
 

class CategoriaController extends Controller
{
    public function index()
    {
        $orderColumn = request('order_column', 'created_at');
        if (!in_array($orderColumn, ['id_categoria', 'nombre', 'created_at'])) {
            $orderColumn = 'created_at';
        }
        $orderDirection = request('order_direction', 'desc');
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }
        $categories = categoria::
            when(request('search_id'), function ($query) {
                $query->where('id_categoria', request('search_id'));
            })
            ->when(request('search_title'), function ($query) {
                $query->where('nombre', 'like', '%' . request('search_title') . '%');
            })
            ->when(request('search_global'), function ($query) {
                $query->where(function ($q) {
                    $q->where('id_categoria', request('search_global'))
                        ->orWhere('nombre', 'like', '%' . request('search_global') . '%');

                });
            })
            ->orderBy($orderColumn, $orderDirection)
            ->paginate(50);
        return CategoriaResource::collection($categories);
    }

    public function store(StoreCategoriaRequest $request)
    {
        $this->authorize('categoria-create');
        $categoria = Categoria::create($request->validated());

        return new CategoriaResource($categoria);
    }

    public function show(Categoria $categoria)
    {
        $this->authorize('categoria-edit');
        return new CategoryResource($categoria);
    }

    public function update(Category $categoria, StoreCategoryRequest $request)
    {
        $this->authorize('categoria-edit');
        $categoria->update($request->validated());

        return new CategoriaResource($categoria);
    }

    public function destroy(Categoria $categoria) {
        $this->authorize('categoria-delete');
        $categoria->delete();

        return response()->noContent();
    }

    public function getList()
    {
        return CategoriaResource::collection(Categoria::all());
    }
}
