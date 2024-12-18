<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Servicio extends Model
{
    use HasFactory;

    protected $table = 'servicios';

    protected $primaryKey = 'id_servicio';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'duracion',
        'idnegocio'
    ];

    public function negocio(){
        return $this->belongsTo(Negocio::class, 'idnegocio', 'id_negocio');
    }

    public function citas(){
        return $this->belongsToMany(Cita::class, 'citas_servicios', 'id_servicio', 'id_cita')
                    ->withPivot('cantidad');
    }
}
