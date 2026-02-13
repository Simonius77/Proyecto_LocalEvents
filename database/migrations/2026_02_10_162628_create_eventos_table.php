<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id('id_evento'); // PK: Identificador
            $table->string('nombre'); // Nombre del evento
            $table->text('descripcion'); // Descripción
            $table->decimal('latitud', 10, 8); // Ubicación
            $table->decimal('longitud', 11, 8); // Ubicación
            $table->decimal('precio', 8, 2); // Precio
            $table->integer('aforo'); // Capacidad máxima
            $table->enum('limite_edad', ['+18', 'todas']); // +18 / todas
            $table->dateTime('fecha_inicio'); // Inicio
            $table->dateTime('fecha_fin'); // Fin

            // FK Categoría
            $table->unsignedBigInteger('id_categoria');
            $table->foreign('id_categoria')->references('id_categoria')->on('categorias')
                ->onDelete('cascade'); // Assuming cascade delete or similar behavior is desired, usually good practice.

            // FK Usuario organizador
            $table->unsignedBigInteger('id_organizador');
            $table->foreign('id_organizador')->references('id_usuario')->on('usuarios')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
