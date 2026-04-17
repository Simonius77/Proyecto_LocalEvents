<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Este es el modelo que representa las categorias de los eventos
class Categoria extends Model
{
    use HasFactory;

    // Indica el nombre de la tabla en la base de datos
    protected $table = 'categorias';
    
    // Marca id_categoria como la clave primaria de la tabla
    protected $primaryKey = 'id_categoria';

    // Permite el llenado masivo de estos campos
    protected $fillable = [
        'nombre',
    ];

    /**
     * Trae todos los eventos que pertenecen a esta categoria
     */
    public function eventos()
    {
        return $this->hasMany(Evento::class, 'id_categoria', 'id_categoria');
    }
}
// Se ha quitado el cierre de PHP para seguir las buenas practicas de Laravel
