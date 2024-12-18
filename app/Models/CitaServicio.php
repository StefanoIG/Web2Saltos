<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CitaServicio extends Model
{
    protected $table = 'citas_servicios';

    protected $fillable = [
        'id_cita',
        'id_servicio',
        'cantidad',
    ];

    public function cita(){
        return $this->belongsTo(Cita::class, 'id_cita', 'id_cita');
    }

    public function servicio(){
        return $this->belongsTo(Servicio::class, 'id_servicio', 'id_servicio');
    }
}
