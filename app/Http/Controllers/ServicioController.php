<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Servicio;
use App\Models\Negocio;

class ServicioController extends Controller
{
    public function update(Request $request, $id)
    {
    $request->validate([
        'nombre' => 'required|string|max:255',
        'descripcion' => 'required|string|max:1000',
        'precio' => 'required|numeric|min:0',
        'duracion' => 'required|integer|min:0',
    ]);

    $servicio = Servicio::findOrFail($id);
    $servicio->update($request->all());

    return response()->json(['message' => 'Servicio actualizado correctamente', 'data' => $servicio]);
    }

    public function store(Request $request, $negocioId)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:1000',
            'precio' => 'required|numeric',
            'duracion' => 'required|integer',
        ]);

        $negocio = Negocio::find($negocioId);

        if (!$negocio) {
            return response()->json([
                'message' => 'Negocio no encontrado.'
            ], 404);
        }

        $servicio = new Servicio();
        $servicio->nombre = $validatedData['nombre'];
        $servicio->descripcion = $validatedData['descripcion'];
        $servicio->precio = $validatedData['precio'];
        $servicio->duracion = $validatedData['duracion'];
        $servicio->idnegocio = $negocio->id_negocio;
        $servicio->save();

        return response()->json([
            'message' => 'Servicio creado exitosamente.',
            'servicio' => $servicio
        ], 201);
    }

    public function destroy($servicioId)
    {
        $servicio = Servicio::find($servicioId);
    
        if (!$servicio) {
            return response()->json(['message' => 'Servicio no encontrado'], 404);
        }
    
        $citasProgramadas = $servicio->citas()->where('estado', 'programado')->exists();
        if ($citasProgramadas) {
            return response()->json([
                'message' => 'No se puede eliminar el servicio porque tiene citas programadas asociadas.'
            ], 400);
        }
    
        $servicio->delete();
    
        return response()->json(['message' => 'Servicio eliminado exitosamente']);
    }
    
}
