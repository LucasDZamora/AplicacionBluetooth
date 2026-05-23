<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class DatosGraficos extends Model
{
    use HasFactory;
    protected $table='nodo_establecimientos';
    protected $primaryKey ='id';
    protected $fillable = [
        'S1_h',
        'S1_p',
        'S1_v',
        'S1_t',
        'S2_r',
        'S2_n',
        'S3_n',
        'S4_long',
        'S4_lat',
        'S4_a',
        'S4_v',
        'S4_h',
        'S5_i',
        'S6_t',
        'S7_c02',
        'S8_n',
        'S9_rtc',
        'S10_f',
        'nodo',
        'reading_time',
        'fecha_unix'
    ];
}
