<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobFallidoVeeam extends Model
{
    use HasFactory;

    protected $table = 'job_fallido_veeam';
    protected $fillable = ['nombre'];
}