<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Incidentes: fecha_cambio_a_exitoso + actualizado_por
        Schema::table('incidentes', function (Blueprint $table) {
            $table->timestamp('fecha_cambio_a_exitoso')->nullable()->after('estado_incidente_id');
            if (!Schema::hasColumn('incidentes', 'actualizado_por')) {
                $table->string('actualizado_por', 100)->nullable()->after('creado_por');
            }
        });

        // Requerimientos: fecha_cierre_dia
        Schema::table('requerimientos', function (Blueprint $table) {
            $table->date('fecha_cierre_dia')->nullable()->after('updated_at');
        });

        // Reportes Veeam: es_informativo + fecha_cierre_informativo + fecha_cambio_a_resuelto
        Schema::table('reportes_veeam', function (Blueprint $table) {
            $table->boolean('es_informativo')->default(false)->after('estado_ticket');
            $table->date('fecha_cierre_informativo')->nullable()->after('es_informativo');
            $table->timestamp('fecha_cambio_a_resuelto')->nullable()->after('fecha_cierre_informativo');
        });
    }

    public function down(): void
    {
        Schema::table('incidentes', function (Blueprint $table) {
            $table->dropColumn(['fecha_cambio_a_exitoso', 'actualizado_por']);
        });

        Schema::table('requerimientos', function (Blueprint $table) {
            $table->dropColumn('fecha_cierre_dia');
        });

        Schema::table('reportes_veeam', function (Blueprint $table) {
            $table->dropColumn(['es_informativo', 'fecha_cierre_informativo', 'fecha_cambio_a_resuelto']);
        });
    }
};
