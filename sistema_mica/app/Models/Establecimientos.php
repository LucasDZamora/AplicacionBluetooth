<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Establecimientos extends Model
{
    use HasFactory;
    protected $table='establecimientos';
    protected $primaryKey ='id_establecimiento';
    protected $fillable = [
        'nombre',
        'rbd',
        'comuna'
    ];
}
