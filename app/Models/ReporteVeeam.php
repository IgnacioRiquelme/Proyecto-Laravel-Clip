<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReporteVeeam extends Model
{
    use HasFactory;

    protected $table = 'reportes_veeam';
    
    protected $fillable = [
        'numero_ticket',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'job_fallido',
        'seguimiento',
        'descripcion_error',
        'estado_ticket',
        'creado_por'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];
}