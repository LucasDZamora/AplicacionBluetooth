<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class TicketSalida extends Model
{
    use HasFactory;
    protected $table='ticket_salida';
    protected $primaryKey ='id_ticket';
    protected $fillable = [
        'fecha_nac',
        'curso',
        'p1',
        'p2'
    ];
}
