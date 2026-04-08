<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategoriasTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('categorias')->delete();
        
        \DB::table('categorias')->insert(array (
            0 => 
            array (
                'id_categoria' => 1,
                'nombre' => 'Tecnología',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id_categoria' => 2,
                'nombre' => 'Programación',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id_categoria' => 3,
                'nombre' => 'Diseño Web',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id_categoria' => 4,
                'nombre' => 'Tutoriales',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id_categoria' => 5,
                'nombre' => 'Noticias',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'id_categoria' => 6,
                'nombre' => 'Opinión',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            6 => 
            array (
                'id_categoria' => 7,
                'nombre' => 'Recursos',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            7 => 
            array (
                'id_categoria' => 8,
                'nombre' => 'Laravel',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            8 => 
            array (
                'id_categoria' => 9,
                'nombre' => 'Vue.js',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            9 => 
            array (
                'id_categoria' => 10,
                'nombre' => 'General',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}