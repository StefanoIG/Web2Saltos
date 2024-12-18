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
        Schema::create('horarios_negocios', function (Blueprint $table) {
            $table->BigIncrements('id_horarios_negocio');
            $table->enum('dias', ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo']);
            $table->time('apertura');
            $table->time('cierre');
            $table->timestamps();

            //Claves foraneas
            $table->foreignId('idnegocio')->references('id_negocio')->on('negocios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios_negocios');
    }
};