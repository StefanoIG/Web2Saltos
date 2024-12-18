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
        Schema::create('citas_confirmaciones', function (Blueprint $table) {
            $table->BigIncrements('id_citas_confirmaciones');
            $table->timestamp('fecha_confirmacion')->nullable();
            $table->timestamps();

            //Claves foraneas
            $table->foreignId('confirmado_por_admin')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('idcita')->references('id_cita')->on('citas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas_confirmaciones');
    }
};
