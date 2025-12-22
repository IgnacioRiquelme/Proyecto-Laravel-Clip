<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EscaladoIncidente extends Model
{
    protected $table = 'escalados_incidentes';
    
    protected $fillable = [
        'nombre'
    ];
}
