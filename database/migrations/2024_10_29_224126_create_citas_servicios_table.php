<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('citas_servicios', function (Blueprint $table) {
            $table->integer('cantidad')->default(1);
            $table->timestamps();

            //Claves foraneas
            $table->foreignId('id_cita')->references('id_cita')->on('citas')->onDelete('cascade');
            $table->foreignId('id_servicio')->references('id_servicio')->on('servicios')->onDelete('cascade');

            //Clave primaria compuesta
            $table->primary(['id_cita', 'id_servicio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas_servicios');
    }
};
