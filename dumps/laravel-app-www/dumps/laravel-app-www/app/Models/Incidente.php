<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Incidente extends Model
{
    use HasFactory;

    protected $fillable = [
        'proceso_id',
        'requerimiento',
        'negocio_incidente_id',
        'ambiente_incidente_id',
        'capa_incidente_id',
        'servidor_incidente_id',
        'escalado_incidente_id',
        'seguimiento',
        'estado_incidente_id',
        'evento_incidente_id',
        'accion_incidente_id',
        'descripcion_evento',
        'solucion',
        'observaciones',
        'inicio',
        'registro',
        'fin',
        'created_at',
        'updated_at',
    ];

    public function proceso()
    {
        return $this->belongsTo(ProcesoMantenimiento::class, 'proceso_id');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoIncidente::class, 'estado_incidente_id');
    }

    public function negocio()
    {
        return $this->belongsTo(NegocioIncidente::class, 'negocio_incidente_id');
    }

    public function ambiente()
    {
        return $this->belongsTo(AmbienteIncidente::class, 'ambiente_incidente_id');
    }

    public function capa()
    {
        return $this->belongsTo(CapaIncidente::class, 'capa_incidente_id');
    }

    public function servidor()
    {
        return $this->belongsTo(ServidorIncidente::class, 'servidor_incidente_id');
    }

    public function evento()
    {
        return $this->belongsTo(EventoIncidente::class, 'evento_incidente_id');
    }

    public function accion()
    {
        return $this->belongsTo(AccionIncidente::class, 'accion_incidente_id');
    }

    public function escalado()
    {
        return $this->belongsTo(EscaladoIncidente::class, 'escalado_incidente_id');
    }
}