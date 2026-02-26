<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Clase UsersTableSeeder
 * Los seeders son para crear datos iniciales en la base de datos, 
 * por ejemplo para pruebas.
 * Este seeder se encarga de poblar la tabla 'usuarios' con datos iniciales
 * para que la aplicación tenga cuentas de acceso por defecto (Admin y User).
 */
class UsersTableSeeder extends Seeder
{
    /**
     * Ejecuta el seeder de la base de datos.
     *
     * @return void
     */
    public function run()
    {
        // Limpiamos la tabla 'usuarios' antes de insertar los nuevos datos
        // ¡CUIDADO!: Esto borrará todos los usuarios existentes en la tabla.
        DB::table('usuarios')->delete();

        // Insertamos los usuarios iniciales del sistema
        DB::table('usuarios')->insert(array (
            // Usuario con rol de Administrador
            0 =>
            array (
                'id_usuario' => 1,
                'nombre' => 'Admin',
                'apellidos' => 'Administrador',
                'email' => 'admin@demo.com',
                'password' => bcrypt('12345678'), // Encriptamos la contraseña para seguridad
                'rol' => 'administrador',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            // Usuario con rol de Usuario estándar
            1 =>
            array (
                'id_usuario' => 2,
                'nombre' => 'User',
                'apellidos' => 'Usuario',
                'email' => 'user@demo.com',
                'password' => bcrypt('12345678'), // Misma contraseña por defecto para pruebas
                'rol' => 'usuario',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            // Usuario Simon con rol de Administrador
             2 =>
            array (
                'id_usuario' => 3,
                'nombre' => 'Simon',
                'apellidos' => 'Catalina Garcia',
                'email' => 'simoncatalinafp@ibf.cat',
                'password' => bcrypt('12345678'), // Encriptamos la contraseña para seguridad
                'rol' => 'administrador',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
        ));
    }
}
