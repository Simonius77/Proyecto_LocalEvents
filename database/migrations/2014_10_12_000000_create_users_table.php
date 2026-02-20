<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

//Esta migración crea la tabla 'usuarios' con campos para almacenar información de los usuarios, 
//incluyendo nombre, apellidos, teléfono, email, contraseña, ubicación, fecha de nacimiento, rol y estado activo. 
//También incluye timestamps para registrar la creación y actualización de los registros.
return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario'); // PK: Identificador Unico
            $table->string('nombre'); // Nombre del usuario
            $table->string('apellidos'); // Apellidos
            $table->string('telefono')->nullable(); // Telefono
            $table->string('email')->unique(); // Email (unico)
            $table->string('password'); // Contraseña encriptada
            $table->decimal('latitud', 10, 8)->nullable(); // Ubicación
            $table->decimal('longitud', 11, 8)->nullable(); // Ubicación
            $table->date('fecha_nacimiento')->nullable(); // Para validar edad
            $table->enum('rol', ['usuario', 'organizador', 'administrador'])->default('usuario'); // Enum rol
            $table->boolean('activo')->default(true); // Para bajas lógicas
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
};
