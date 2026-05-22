<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Experimentos extends Model
{
    use HasFactory;
    protected $table='experimentos';
    protected $primaryKey ='id_experimentos';
    protected $fillable = [
        'nombre',
        'ema',
        'fecha_inicio',
        'inicio_unix',
        'fecha_termino',
        'termino_unix',
        'descripcion',
        'id_establecimiento',
        'estado'
    ];
}
