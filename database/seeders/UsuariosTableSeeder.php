<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsuariosTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('usuarios')->delete();
        
        \DB::table('usuarios')->insert(array (
            0 => 
            array (
                'id_usuario' => 1,
                'nombre' => 'Admin',
                'apellidos' => 'Administrador',
                'telefono' => NULL,
                'email' => 'admin@demo.com',
                'password' => '$2y$10$LxEp9WyXi2JSbMSF195fHeoetVav.ZI/8lz15/KB5OX8R0tPdJw9O',
                'latitud' => NULL,
                'longitud' => NULL,
                'fecha_nacimiento' => NULL,
                'rol' => 'administrador',
                'activo' => 1,
                'remember_token' => NULL,
                'created_at' => '2026-02-25 18:53:24',
                'updated_at' => '2026-03-18 17:59:43',
            ),
            1 => 
            array (
                'id_usuario' => 2,
                'nombre' => 'User',
                'apellidos' => 'Usuario',
                'telefono' => NULL,
                'email' => 'user@demo.com',
                'password' => '$2y$10$aeZw8Zu/vo7Wm3MY8TZSEuYuU201kQbN1Q/o29iXTO.i.7WgURIhG',
                'latitud' => NULL,
                'longitud' => NULL,
                'fecha_nacimiento' => NULL,
                'rol' => 'usuario',
                'activo' => 1,
                'remember_token' => NULL,
                'created_at' => '2026-02-25 18:53:24',
                'updated_at' => '2026-02-25 18:53:24',
            ),
            2 => 
            array (
                'id_usuario' => 3,
                'nombre' => 'Simon',
                'apellidos' => 'Garcia Garcia',
                'telefono' => NULL,
                'email' => 'simoncatalinafp@ibf.cat',
                'password' => '$2y$10$yh/25X8.hwhtPEz08Df5Y.wkUlaJAtvfYVuCWGaR0XbfCjk8bcRhe',
                'latitud' => NULL,
                'longitud' => NULL,
                'fecha_nacimiento' => NULL,
                'rol' => 'administrador',
                'activo' => 1,
                'remember_token' => NULL,
                'created_at' => '2026-02-26 14:36:26',
                'updated_at' => '2026-03-19 14:52:31',
            ),
            3 => 
            array (
                'id_usuario' => 4,
                'nombre' => 'Test',
                'apellidos' => 'User',
                'telefono' => NULL,
                'email' => 'final_test@example.com',
                'password' => '$2y$10$Dp/.EUXA34rW4H0XEjr2O./xQ60CcSpBeHOpia2Ft4wi3wQCHT1DW',
                'latitud' => NULL,
                'longitud' => NULL,
                'fecha_nacimiento' => NULL,
                'rol' => 'usuario',
                'activo' => 1,
                'remember_token' => NULL,
                'created_at' => '2026-03-19 14:44:44',
                'updated_at' => '2026-03-19 14:44:44',
            ),
            4 => 
            array (
                'id_usuario' => 5,
                'nombre' => 'Org',
                'apellidos' => 'Prueba',
                'telefono' => NULL,
                'email' => 'organizador@demo.com',
                'password' => '$2y$10$TfeetMFIlxV4FDlHbIhmVOrIDY9iy0xnAVobhoU5mS1TXoieToUf2',
                'latitud' => NULL,
                'longitud' => NULL,
                'fecha_nacimiento' => NULL,
                'rol' => 'organizador',
                'activo' => 1,
                'remember_token' => NULL,
                'created_at' => '2026-03-19 15:18:01',
                'updated_at' => '2026-03-19 15:18:01',
            ),
            5 => 
            array (
                'id_usuario' => 6,
                'nombre' => 'Drianny',
                'apellidos' => 'Batalla',
                'telefono' => NULL,
                'email' => 'drianny@demo.com',
                'password' => '$2y$10$/vTHJ9LdASD4WCX7CYbqluyqMUnjRO.0fDrTM9Dl01ClfOrh97Y/O',
                'latitud' => NULL,
                'longitud' => NULL,
                'fecha_nacimiento' => NULL,
                'rol' => 'usuario',
                'activo' => 1,
                'remember_token' => NULL,
                'created_at' => '2026-03-27 16:35:34',
                'updated_at' => '2026-03-27 16:35:34',
            ),
        ));
        
        
    }
}