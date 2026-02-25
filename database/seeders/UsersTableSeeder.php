<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
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
                'email' => 'admin@demo.com',
                'password' => bcrypt('12345678'),
                'rol' => 'administrador',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            1 =>
            array (
                'id_usuario' => 2,
                'nombre' => 'User',
                'apellidos' => 'Usuario',
                'email' => 'user@demo.com',
                'password' => bcrypt('12345678'),
                'rol' => 'usuario',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
        ));


    }
}
