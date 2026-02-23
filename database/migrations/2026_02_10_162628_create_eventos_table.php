<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migracion para crear la tabla de eventos
// Esta tabla almacena la informacion principal de los eventos locales
return new class extends Migration {
    /**
     * Ejecuta las migraciones para crear la tabla eventos.
     */
    public function up(): void
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id('id_evento'); // Identificador unico del evento
            $table->string('nombre'); // Nombre del evento
            $table->text('descripcion'); // Descripcion detallada

            // Nota: Para guardar imagenes se recomienda usar Spatie MediaLibrary
            // lo que evita tener que añadir una columna 'imagen' aqui.
            // Si se quisiera usar una columna simple, se podria añadir:
            // $table->string('imagen')->nullable();

            $table->decimal('latitud', 10, 8); // Coordenada de latitud para el mapa
            $table->decimal('longitud', 11, 8); // Coordenada de longitud para el mapa
            $table->decimal('precio', 8, 2); // Precio de la entrada o participacion
            $table->integer('aforo'); // Capacidad maxima de personas
            $table->enum('limite_edad', ['+18', 'todas']); // Restriccion de edad
            $table->dateTime('fecha_inicio'); // Fecha y hora de inicio
            $table->dateTime('fecha_fin'); // Fecha y hora de finalizacion

            // Relacion con la tabla categorias
            $table->unsignedBigInteger('id_categoria');
            $table->foreign('id_categoria')->references('id_categoria')->on('categorias')
                ->onDelete('cascade');

            // Relacion con la tabla usuarios (organizador)
            $table->unsignedBigInteger('id_organizador');
            $table->foreign('id_organizador')->references('id_usuario')->on('usuarios')
                ->onDelete('cascade');

            $table->timestamps(); // Registros de creacion y actualizacion
        });
    }

    /**
     * Revierte las migraciones eliminando la tabla eventos.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
