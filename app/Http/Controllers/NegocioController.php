<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Negocio; 
use Illuminate\Http\JsonResponse;
use App\Models\HorarioNegocio;
use Illuminate\Support\Facades\DB;
use App\Models\Cita;


class NegocioController extends Controller
{
    public function index(): JsonResponse
    {
        $negocios = Negocio::all();
        return response()->json($negocios);
    }

    public function show($id)
    {   
        $negocio = Negocio::with(['servicios', 'horarios'])->findOrFail($id);
        return response()->json($negocio);
    }

    public function getNegociosPorUsuario($id)
    {
        $negocios = Negocio::where('iduser', $id)->get();
        return response()->json($negocios);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
        'nombre' => 'required|string|max:255',
        'tipo' => 'required|string|max:255',
        'direccion' => 'required|string|max:255',
        'telefono' => 'required|numeric|digits_between:0,15',
    ]);

        $negocio = Negocio::create(array_merge($validated, ['iduser' => auth()->id()]));

        return response()->json([
        'message' => 'Negocio creado exitosamente',
        'negocio' => $negocio,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
        'nombre' => 'required|string|max:255',
        'tipo' => 'required|string|max:255',
        'direccion' => 'required|string|max:500',
        'telefono' => 'required|numeric|digits_between:0,15',
        ]);

        $negocio = Negocio::findOrFail($id);
        $negocio->update($request->all());

        return response()->json(['message' => 'Negocio actualizado correctamente', 'data' => $negocio]);
    }

    public function borrarNegocio($id)
    {
        $negocio = Negocio::findOrFail($id);
    
        if ($negocio->citas()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el negocio porque tiene citas asociadas.'
            ], 400);
        }
    
        $negocio->delete();
    
        return response()->json(['message' => 'Negocio borrado correctamente']);
    }

    
    public function verificarDiaExistente($negocioId, $dia)
    {
        $horario = HorarioNegocio::where('idnegocio', $negocioId)
                                ->where('dias', $dia)
                                ->first();

        return response()->json(['existe' => $horario !== null]);
    }

    public function crearHorario(Request $request, $negocioId)
    {
    $validated = $request->validate([
        'dia' => 'required|string|in:lunes,martes,miercoles,jueves,viernes,sabado,domingo',
        'apertura' => 'required|date_format:H:i',
        'cierre' => 'required|date_format:H:i|after:apertura',
    ]);

    $horario = new HorarioNegocio([
        'idnegocio' => $negocioId,
        'dias' => $validated['dia'],
        'apertura' => $validated['apertura'],
        'cierre' => $validated['cierre'],
    ]);

    $horario->save();

    return response()->json(['message' => 'Horario creado exitosamente', 'horario' => $horario]);
}

    public function getCitasPorEstado(Request $request, $id)
    {
    $userId = $request->user()->id;

    $negocios = Negocio::select('negocios.id_negocio', 'negocios.nombre', 
        DB::raw('sum(case when citas.estado = \'programado\' then 1 else 0 end) as programado'),
        DB::raw('sum(case when citas.estado = \'completado\' then 1 else 0 end) as completado'),
        DB::raw('sum(case when citas.estado = \'cancelado\' then 1 else 0 end) as cancelado'),
        DB::raw('sum(case when citas.estado = \'expirado\' then 1 else 0 end) as expirado')
    )
    ->leftJoin('citas', 'citas.idnegocio', '=', 'negocios.id_negocio')
    ->where('negocios.iduser', $userId)
    ->groupBy('negocios.id_negocio')
    ->get();

    return response()->json($negocios);
}

    public function getCitasPorNegocio($idNegocio)
    {
        $citas = Cita::where('idnegocio', $idNegocio)
            ->with('user')
            ->get();

        $citas = $citas->map(function ($cita) {
        return [
            'id' => $cita->id_cita,
            'fecha' => $cita->fecha,
            'hora' => $cita->hora,
            'descripcion' => $cita->descripcion,
            'estado' => $cita->estado,
            'usuario' => [
                'nombre' => $cita->user->name,
                'cedula' => $cita->user->cedula,
                'numero' => $cita->user->numero,
            ],
            'codigo'=> $cita->codigo,
            'notas_administrador' => $cita->notas_administrador,
        ];
    });

    return response()->json($citas);
}

}
