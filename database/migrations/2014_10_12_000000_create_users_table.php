<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario'); // PK: Identificador único
            $table->string('nombre'); // Nombre del usuario
            $table->string('apellidos'); // Apellidos
            $table->string('telefono')->nullable(); // Teléfono
            $table->string('email')->unique(); // Email (único)
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
