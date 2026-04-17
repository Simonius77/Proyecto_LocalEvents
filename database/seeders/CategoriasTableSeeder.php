<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategoriasTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('categorias')->delete();
        
        $now = Carbon::now();

        DB::table('categorias')->insert(array (
            0 => 
            array (
                'id_categoria' => 1,
                'nombre' => 'Conciertos',
                'created_at' => $now,
                'updated_at' => $now,
            ),
            1 => 
            array (
                'id_categoria' => 2,
                'nombre' => 'Teatro',
                'created_at' => $now,
                'updated_at' => $now,
            ),
            2 => 
            array (
                'id_categoria' => 3,
                'nombre' => 'Gastronomia',
                'created_at' => $now,
                'updated_at' => $now,
            ),
            3 => 
            array (
                'id_categoria' => 4,
                'nombre' => 'Exposiciones',
                'created_at' => $now,
                'updated_at' => $now,
            ),
            4 => 
            array (
                'id_categoria' => 5,
                'nombre' => 'Deportes',
                'created_at' => $now,
                'updated_at' => $now,
            ),
        ));
        
        
    }
}