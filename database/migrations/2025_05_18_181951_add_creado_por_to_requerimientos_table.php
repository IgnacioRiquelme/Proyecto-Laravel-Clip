<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('requerimientos', function (Blueprint $table) {
            $table->string('creado_por')->nullable()->after('observaciones');
        });
    }

    public function down(): void
    {
        Schema::table('requerimientos', function (Blueprint $table) {
            $table->dropColumn('creado_por');
        });
    }
};
