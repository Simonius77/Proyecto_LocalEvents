<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class CategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('categorias')->delete();
        
        DB::table('categorias')->insert(array (
            0 => 
            array (
                'id_categoria' => 1,
                'nombre' => 'Tecnología',
            ),
            1 => 
            array (
                'id_categoria' => 2,
                'nombre' => 'Programación',
            ),
            2 => 
            array (
                'id_categoria' => 3,
                'nombre' => 'Diseño Web',
            ),
            3 => 
            array (
                'id_categoria' => 4,
                'nombre' => 'Tutoriales',
            ),
            4 => 
            array (
                'id_categoria' => 5,
                'nombre' => 'Noticias',
            ),
            5 => 
            array (
                'id_categoria' => 6,
                'nombre' => 'Opinión',
            ),
            6 => 
            array (
                'id_categoria' => 7,
                'nombre' => 'Recursos',
            ),
            7 => 
            array (
                'id_categoria' => 8,
                'nombre' => 'Laravel',
            ),
            8 => 
            array (
                'id_categoria' => 9,
                'nombre' => 'Vue.js',
            ),
            9 => 
            array (
                'id_categoria' => 10,
                'nombre' => 'General',
            ),
        ));
        
        
    }
}