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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id('id_pago'); // PK: Identificador

            // FK Reserva
            $table->unsignedBigInteger('id_reserva');
            $table->foreign('id_reserva')->references('id_reserva')->on('reservas')
                ->onDelete('cascade');

            $table->decimal('monto', 8, 2); // Monto
            $table->dateTime('fecha_pago'); // Fecha pago
            $table->enum('estado', ['pagado', 'reembolsado'])->default('pagado'); // Estado

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
