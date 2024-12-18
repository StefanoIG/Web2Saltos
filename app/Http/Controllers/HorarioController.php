<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HorarioNegocio;

class HorarioController extends Controller
{
    public function update(Request $request, $id)
    {
    $request->validate([
        'dias' => 'required|in:lunes,martes,miercoles,jueves,viernes,sabado,domingo',
        'apertura' => 'required',
        'cierre' => 'required',
    ]);

    $horario = HorarioNegocio::findOrFail($id);
    $horario->update($request->all());

    return response()->json(['message' => 'Horario actualizado correctamente', 'data' => $horario]);
}

    public function destroy($horarioId)
    {
    $horario = HorarioNegocio::find($horarioId);

    if (!$horario) {
        return response()->json(['message' => 'Horario no encontrado'], 404);
    }

    $horario->delete();

    return response()->json(['message' => 'Horario eliminado exitosamente']);
}

}
