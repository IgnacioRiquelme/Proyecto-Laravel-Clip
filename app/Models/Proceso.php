<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proceso extends Model
{
    protected $table = 'procesos_mantenimiento'; // <--- usa la tabla correcta

    protected $fillable = [
    'trabajo',
    'schedulix_id',
    'categoria_1',
    'categoria_2',
    'ruta_completa',
    'responsable_escalamiento',
    'observacion',
    'requiere_solucion_inmediata',
];
}