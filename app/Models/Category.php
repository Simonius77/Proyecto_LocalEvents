<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categorias';
    protected $primaryKey = 'id_categoria';

    protected $fillable = [
        'nombre',
    ];

    /**
     * Obtener los eventos de la categoria
     */
    public function eventos()
    {
        return $this->hasMany(evento::class, 'id_categoria', 'id_categoria');
    }


}
