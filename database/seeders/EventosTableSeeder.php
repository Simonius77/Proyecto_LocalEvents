<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class EventosTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('eventos')->delete();
        
        DB::table('eventos')->insert(array (
            0 => 
            array (
                'id_evento' => 1,
                'nombre' => 'evento1',
                'descripcion' => 'este es un evento de prueba 1',
                'localizacion' => NULL,
                'latitud' => '40.41680000',
                'longitud' => '-3.70380000',
                'precio' => '10.00',
                'aforo' => 100,
                'limite_edad' => '+18',
                'fecha_inicio' => '2026-03-05 18:07:02',
                'fecha_fin' => '2026-03-05 22:07:02',
                'id_categoria' => 1,
                'id_organizador' => 1,
                'created_at' => '2026-03-04 18:07:02',
                'updated_at' => '2026-03-04 18:07:02',
            ),
            1 => 
            array (
                'id_evento' => 2,
                'nombre' => 'evento2',
                'descripcion' => 'este es un evento de prueba 2',
                'localizacion' => NULL,
                'latitud' => '40.41680000',
                'longitud' => '-3.70380000',
                'precio' => '20.00',
                'aforo' => 200,
                'limite_edad' => '+18',
                'fecha_inicio' => '2026-03-06 18:07:04',
                'fecha_fin' => '2026-03-06 22:07:04',
                'id_categoria' => 1,
                'id_organizador' => 1,
                'created_at' => '2026-03-04 18:07:04',
                'updated_at' => '2026-03-04 18:07:04',
            ),
            2 => 
            array (
                'id_evento' => 3,
                'nombre' => 'evento3',
                'descripcion' => 'este es un evento de prueba 3',
                'localizacion' => NULL,
                'latitud' => '40.41680000',
                'longitud' => '-3.70380000',
                'precio' => '30.00',
                'aforo' => 300,
                'limite_edad' => '+18',
                'fecha_inicio' => '2026-03-07 18:07:04',
                'fecha_fin' => '2026-03-07 22:07:04',
                'id_categoria' => 1,
                'id_organizador' => 1,
                'created_at' => '2026-03-04 18:07:04',
                'updated_at' => '2026-03-04 18:07:04',
            ),
            3 => 
            array (
                'id_evento' => 4,
                'nombre' => 'concierto',
                'descripcion' => 'cancion',
                'localizacion' => 'C26G+8Q Molins de Rei',
                'latitud' => '41.41076440',
                'longitud' => '2.02728210',
                'precio' => '67.00',
                'aforo' => 200,
                'limite_edad' => '+18',
                'fecha_inicio' => '2026-04-16 20:29:00',
                'fecha_fin' => '2026-05-16 17:26:00',
                'id_categoria' => 1,
                'id_organizador' => 5,
                'created_at' => '2026-04-07 15:26:55',
                'updated_at' => '2026-04-07 15:26:55',
            ),
        ));
        
        
    }
}