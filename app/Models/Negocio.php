<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Servicio;

class Negocio extends Model
{
    use HasFactory;

    protected $table = 'negocios';
    
    protected $primaryKey = 'id_negocio';

    protected $fillable = [
        'nombre',
        'tipo',
        'direccion',
        'telefono',
        'iduser'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'iduser', 'id');
    }

    public function servicios(){
        return $this->hasMany(Servicio::class, 'idnegocio', 'id_negocio');
    }

    public function horarios(){
    return $this->hasMany(HorarioNegocio::class, 'idnegocio', 'id_negocio');
    }
    
    public function citas(){
    return $this->hasMany(Cita::class, 'idnegocio', 'id_negocio');
    }
}
