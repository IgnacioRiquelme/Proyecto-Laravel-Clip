<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    use HasFactory;

    protected $table = 'grupos';

    protected $fillable = [
        'nombre',
        'color',
        'created_at',
        'updated_at',
    ];

    public function procesos()
    {
        return $this->hasMany(Proceso::class, 'grupo_id');
    }
}
