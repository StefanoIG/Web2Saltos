<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HorarioNegocio extends Model
{
    use HasFactory;

    protected $table = 'horarios_negocios';

    protected $primaryKey = 'id_horarios_negocio';

    protected $fillable = [
        'dias',
        'apertura',
        'cierre',
        'idnegocio'
    ];

    public function negocio(){
        return $this->belongsTo(Negocio::class, 'idnegocio', 'id_negocio');
    }
    
}
