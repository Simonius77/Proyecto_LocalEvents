<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\categoria;

class CategoryController extends Controller
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
        return CategoryResource::collection($categories);
    }

    public function store(StoreCategoryRequest $request)
    {
        $this->authorize('category-create');
        $category = categoria::create($request->validated());

        return new CategoryResource($category);
    }

    public function show(categoria $category)
    {
        $this->authorize('category-edit');
        return new CategoryResource($category);
    }

    public function update(categoria $category, StoreCategoryRequest $request)
    {
        $this->authorize('category-edit');
        $category->update($request->validated());

        return new CategoryResource($category);
    }

    public function destroy(categoria $category)
    {
        $this->authorize('category-delete');
        $category->delete();

        return response()->noContent();
    }

    public function getList()
    {
        return CategoryResource::collection(categoria::all());
    }
}
