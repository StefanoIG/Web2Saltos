<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Negocio;
use App\Models\User;
use App\Models\CitaConfirmacion;
use Ramsey\Uuid\Uuid;

class Cita extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cita) {
            // Genera un UUID y lo asigna al atributo 'codigo'
            $cita->codigo = Uuid::uuid4()->toString();
        });
    }

    protected $table = 'citas';

    protected $primaryKey = 'id_cita';

    protected $fillable = [
        'fecha',
        'hora',
        'descripcion',
        'notas_administrador',
        'estado',
        'iduser',
        'idnegocio',
        'codigo'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'iduser', 'id');
    }

    public function negocio(){
        return $this->belongsTo(Negocio::class, 'idnegocio', 'id_negocio');
    }

    public function servicios()
    {
        return $this->belongsToMany(Servicio::class, 'citas_servicios', 'id_cita', 'id_servicio')
                    ->withPivot('cantidad');
    }
    public function confirmacion(){
    return $this->hasOne(CitaConfirmacion::class, 'idcita', 'id_cita');
    }

}
