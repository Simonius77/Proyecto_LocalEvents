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
        Schema::create('reservas', function (Blueprint $table) {
            $table->id('id_reserva'); // PK: Identificador

            // FK Usuario
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')
                ->onDelete('cascade');

            // FK Evento
            $table->unsignedBigInteger('id_evento');
            $table->foreign('id_evento')->references('id_evento')->on('eventos')
                ->onDelete('cascade');

            $table->dateTime('fecha_reserva'); // Fecha reserva
            $table->enum('estado', ['reservada', 'cancelada'])->default('reservada'); // Estado

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
