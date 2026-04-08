<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Soy el modelo que representa las categorias de los eventos
class Categoria extends Model
{
    use HasFactory;

    // Indico el nombre de la tabla en la base de datos
    protected $table = 'categorias';
    
    // Marco id_categoria como la clave primaria de la tabla
    protected $primaryKey = 'id_categoria';

    // Permito el llenado masivo de estos campos
    protected $fillable = [
        'nombre',
    ];

    /**
     * Traigo todos los eventos que pertenecen a esta categoria
     */
    public function eventos()
    {
        return $this->hasMany(Evento::class, 'id_categoria', 'id_categoria');
    }
}
// He quitado el cierre de PHP para seguir las buenas practicas de Laravel
