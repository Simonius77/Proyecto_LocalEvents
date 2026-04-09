<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Limpiar caché de roles y permisos antes de empezar
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Catálogos básicos y Usuarios
        $this->call(CategoriasTableSeeder::class);
        $this->call(UsuariosTableSeeder::class);
        
        // 2. Eventos y derivados
        if (file_exists(__DIR__ . '/EventosTableSeeder.php')) $this->call(EventosTableSeeder::class);
        if (file_exists(__DIR__ . '/ReservasTableSeeder.php')) $this->call(ReservasTableSeeder::class);
        if (file_exists(__DIR__ . '/PagosTableSeeder.php')) $this->call(PagosTableSeeder::class);

        $this->call(MediaTableSeeder::class);
        
        // 3. Roles y configuración de permisos
        $this->call(RolesTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(RoleHasPermissionsTableSeeder::class);
        $this->call(ModelHasRolesTableSeeder::class);
        $this->call(ModelHasPermissionsTableSeeder::class);
    }
}
