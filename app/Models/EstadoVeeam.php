<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoVeeam extends Model
{
    use HasFactory;

    protected $table = 'estado_veeam';
    protected $fillable = ['nombre'];
}