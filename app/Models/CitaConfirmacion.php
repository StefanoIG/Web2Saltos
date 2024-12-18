<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CitaConfirmacion extends Model
{
    use HasFactory;

    protected $table = 'citas_confirmaciones';

    protected $primaryKey = 'id_citas_confirmaciones';

    protected $fillable = [
        'confirmado_por_admin',
        'fecha_confirmacion',
        'idcita'
    ];

    public function cita(){
        return $this->belongsTo(Cita::class, 'idcita', 'id_cita');
    }
    
    public function usuarioConfirmador(){
    return $this->belongsTo(User::class, 'confirmado_por_admin');
    }

}
