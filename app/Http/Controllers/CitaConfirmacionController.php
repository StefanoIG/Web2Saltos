<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\CitaConfirmacion;

class CitaConfirmacionController extends Controller
{
    public function confirmar($id)
    {
        $cita = Cita::find($id);

        if (!$cita) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        $confirmacion = CitaConfirmacion::create([
            'confirmado_por_admin' => auth()->user()->id,
            'fecha_confirmacion' => now(),
            'idcita' => $id,
        ]);

        $cita->estado = 'completado';
        $cita->save();

        return response()->json(['message' => 'Cita completada exitosamente.', 'confirmacion' => $confirmacion], 200);
    }
}
