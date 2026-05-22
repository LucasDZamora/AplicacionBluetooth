<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Estaciones extends Model
{
    use HasFactory;
    protected $table='estaciones';
    protected $primaryKey ='id_estacion';
    protected $fillable = [
        'id_establecimiento',
        'nombre',
        'mac'
    ];
}
