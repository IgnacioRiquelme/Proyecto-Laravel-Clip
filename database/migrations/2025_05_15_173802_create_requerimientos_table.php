<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requerimientos', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha_hora');
            $table->string('turno');
            $table->string('numero_ticket')->unique();
            $table->text('requerimiento');
            $table->string('solicitante');
            $table->string('negocio');
            $table->string('ambiente');
            $table->string('capa');
            $table->string('servidor');
            $table->string('estado');
            $table->string('tipo_solicitud');
            $table->string('tipo_pase');
            $table->string('ic')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requerimientos');
    }
};


