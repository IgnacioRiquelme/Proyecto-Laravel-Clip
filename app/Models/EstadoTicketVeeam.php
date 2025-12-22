<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoTicketVeeam extends Model
{
    use HasFactory;

    protected $table = 'estado_ticket_veeam';
    protected $fillable = ['nombre'];
}