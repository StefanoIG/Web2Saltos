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
        Schema::create('citas', function (Blueprint $table) {
            $table->BigIncrements('id_cita');
            $table->date('fecha');
            $table->time('hora');
            $table->text('descripcion')->nullable();
            $table->enum('estado', ['programado', 'completado', 'cancelado', 'expirado'])->default('programado');
            $table->string('codigo')->unique()->nullable();
            $table->string('notas_administrador')->nullable();
            $table->timestamps();

            //Claves foraneas
            $table->foreignId('iduser')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('idnegocio')->references('id_negocio')->on('negocios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
