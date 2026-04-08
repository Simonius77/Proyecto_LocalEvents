<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ReservasTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('reservas')->delete();
        
        \DB::table('reservas')->insert(array (
            0 => 
            array (
                'id_reserva' => 1,
                'id_usuario' => 2,
                'id_evento' => 3,
                'cantidad' => 1,
                'total' => '30.00',
                'estado' => 'solicitada_cancelacion',
                'created_at' => '2026-03-19 15:46:40',
                'updated_at' => '2026-03-19 15:47:31',
            ),
            1 => 
            array (
                'id_reserva' => 2,
                'id_usuario' => 2,
                'id_evento' => 1,
                'cantidad' => 1,
                'total' => '10.00',
                'estado' => 'pagado',
                'created_at' => '2026-03-19 15:46:45',
                'updated_at' => '2026-03-19 15:47:20',
            ),
            2 => 
            array (
                'id_reserva' => 3,
                'id_usuario' => 3,
                'id_evento' => 1,
                'cantidad' => 1,
                'total' => '10.00',
                'estado' => 'pendiente',
                'created_at' => '2026-03-24 16:55:53',
                'updated_at' => '2026-03-24 16:55:53',
            ),
            3 => 
            array (
                'id_reserva' => 4,
                'id_usuario' => 3,
                'id_evento' => 1,
                'cantidad' => 1,
                'total' => '10.00',
                'estado' => 'pendiente',
                'created_at' => '2026-03-24 16:55:54',
                'updated_at' => '2026-03-24 16:55:54',
            ),
            4 => 
            array (
                'id_reserva' => 5,
                'id_usuario' => 3,
                'id_evento' => 1,
                'cantidad' => 1,
                'total' => '10.00',
                'estado' => 'pendiente',
                'created_at' => '2026-03-24 16:55:54',
                'updated_at' => '2026-03-24 16:55:54',
            ),
            5 => 
            array (
                'id_reserva' => 6,
                'id_usuario' => 2,
                'id_evento' => 2,
                'cantidad' => 1,
                'total' => '20.00',
                'estado' => 'solicitada_cancelacion',
                'created_at' => '2026-03-26 16:26:54',
                'updated_at' => '2026-03-26 16:27:27',
            ),
            6 => 
            array (
                'id_reserva' => 7,
                'id_usuario' => 3,
                'id_evento' => 1,
                'cantidad' => 1,
                'total' => '10.00',
                'estado' => 'pendiente',
                'created_at' => '2026-04-07 15:20:17',
                'updated_at' => '2026-04-07 15:20:17',
            ),
        ));
        
        
    }
}