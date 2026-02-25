<?php
//Atencion!!!
//Ojo con las variables, yo programo en castellano antiguo by Simon.
/*Los eventos tendran categorias, por lo que se crea esta tabla para relacionarlos*/

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';
    protected $primaryKey = 'id_categoria';

    protected $fillable = [
        'nombre',
    ];

    /**
     * Obtener los eventos de la categoría.
     */
    public function eventos()
    {
        return $this->hasMany(evento::class, 'id_categoria', 'id_categoria');
    }
}
/*por que en este archivo no se cierra el php?
Es una excelente pregunta técnica. En PHP moderno (y especialmente en Laravel), 
es una buena práctica recomendada NO cerrar la etiqueta ?> 
al final de los archivos que contienen solo código PHP.*/
