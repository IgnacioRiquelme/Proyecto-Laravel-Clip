<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requerimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha_hora',
        'turno',
        'numero_ticket',
        'requerimiento',
        'solicitante',
        'negocio',
        'ambiente',
        'capa',
        'servidor',
        'estado',
        'tipo_solicitud',
        'tipo_pase',
        'ic',
        'observaciones'
    ];

    public $timestamps = false; // si no usas created_at / updated_at
}

