<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\Negocio;
use App\Models\Servicio;
use App\Models\CitaConfirmacion;
use Carbon\Carbon;

class CitaController extends Controller
{
    public function agendarCita(Request $request)
    {   
        // Validación de los campos del formulario
        $request->validate([
            'servicios' => 'required|array|min:1',
            'servicios.*.id_servicio' => 'required|exists:servicios,id_servicio',
            'servicios.*.cantidad' => 'required|integer|min:1',
            'fecha' => 'required|date|after_or_equal:' . Carbon::now('America/Bogota')->toDateString(),
            'hora' => 'required|date_format:H:i',
            'id_negocio' => 'required|exists:negocios,id_negocio',
        ], [
            'fecha.after_or_equal' => 'Elige un día valido para la cita.',
            'servicios.required' => 'Elija al menos un servicio.',
        ]);

        $negocio = Negocio::with('horarios')->find($request->id_negocio);

        if (!$negocio) {
            return response()->json(['message' => 'Negocio no encontrado'], 404);
        }

        // Obtener el día de la semana en minúsculas y sin tildes
        $diaSeleccionado = strtolower(Carbon::parse($request->fecha)
            ->locale('es')
            ->isoFormat('dddd'));
        $diaSeleccionado = $this->quitarTildes($diaSeleccionado);

        // Validar si la fecha tiene el formato 'Y-m-d'
        if (!Carbon::createFromFormat('Y-m-d', $request->fecha)->isValid()) {
            return response()->json(['message' => 'Fecha inválida.'], 400);
        }

        // Buscar el horario del negocio para el día seleccionado
        $horarioNegocio = $negocio->horarios->where('dias', $diaSeleccionado)->first();

        if (!$horarioNegocio) {
            return response()->json(['message' => 'El negocio está cerrado este día.'], 400);
        }

        // Verificar si la hora seleccionada está dentro del horario de apertura y cierre
        $horaSeleccionada = Carbon::parse($request->hora);
        $horaApertura = Carbon::parse($horarioNegocio->apertura);
        $horaCierre = Carbon::parse($horarioNegocio->cierre);

        if ($horaSeleccionada->lt($horaApertura) || $horaSeleccionada->gte($horaCierre)) {
            return response()->json(['message' => 'El negocio está cerrado en esa hora.'], 400);
        }

       // Comprobar si ya existe una cita agendada para esa fecha y hora
    foreach ($request->servicios as $servicio) {
    $citaExistente = Cita::where('idnegocio', $request->id_negocio)
        ->where('fecha', $request->fecha)
        ->where('hora', $request->hora)
        ->where('estado', 'programado') // Solo verificar citas con estado 'programado'
        ->whereHas('servicios', function ($query) use ($servicio) {
            $query->where('citas_servicios.id_servicio', $servicio['id_servicio']);
        })
        ->exists();

    if ($citaExistente) {
        return response()->json(['message' => 'Horario ocupado, elija otra hora.'], 400);
    }
}


        // Crear la nueva cita
        $cita = new Cita();
        $cita->idnegocio = $request->id_negocio;
        $cita->fecha = $request->fecha;
        $cita->hora = $request->hora;
        $cita->estado = 'programado'; 
        $cita->iduser = auth()->id();
        $cita->save();

        // Asociar los servicios seleccionados a la cita, con la cantidad especificada
        foreach ($request->servicios as $servicio) {
            $cita->servicios()->attach($servicio['id_servicio'], ['cantidad' => $servicio['cantidad']]);
        }

        return response()->json(['message' => 'Cita agendada correctamente', 'cita' => $cita], 201);
    }

    /**
     * Quita tildes de una cadena de texto.
     *
     * @param string $cadena
     * @return string
     */
    private function quitarTildes($cadena)
    {
        $tildes = ['á', 'é', 'í', 'ó', 'ú'];
        $sinTildes = ['a', 'e', 'i', 'o', 'u'];
        return str_replace($tildes, $sinTildes, $cadena);
    }

    public function obtenerCitasUsuario($id)
    {
        try {
            $citas = Cita::with(['negocio', 'servicios'])
                ->where('iduser', $id)
                ->get();
    
        return response()->json($citas, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener citas'], 500);
        }
    }
    
    public function show($id)
    {
        $cita = Cita::with(['servicios', 'negocio', 'user', 'confirmacion.usuarioConfirmador'])->findOrFail($id);
    
        if (!$cita) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }
    
        return response()->json($cita, 200);
    }
    
    public function actualizarDescripcion(Request $request, $id)
    {
    try {
        $cita = Cita::findOrFail($id);
        $cita->descripcion = $request->input('descripcion');
        $cita->save();

        return response()->json(['message' => 'Descripción actualizada correctamente'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al actualizar la descripción'], 500);
    }
}

    public function cancelarCita($id)
    {
    try {
        $cita = Cita::findOrFail($id);
        
        if ($cita->estado === 'cancelado') {
            return response()->json(['message' => 'La cita ya está cancelada'], 400);
        }
        
        $cita->estado = 'cancelado';
        $cita->save();
        
        return response()->json(['message' => 'Cita cancelada con éxito'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al cancelar la cita'], 500);
    }
}

    public function updateCita(Request $request, $id_cita)
    {
        $cita = Cita::findOrFail($id_cita);
        $cita->update($request->all());
        return response()->json(['message' => 'Cita actualizada con éxito.']);
    }

    public function cancelCita($id_cita)
    {
        $cita = Cita::findOrFail($id_cita);
        $cita->estado = 'cancelado';
        $cita->save();
        return response()->json(['message' => 'Cita cancelada con éxito.']);
    }

    public function expireCita($id_cita)
    {
        $cita = Cita::findOrFail($id_cita);
        $cita->estado = 'expirado';
        $cita->save();
        return response()->json(['message' => 'Cita expirada con éxito.']);
    }
}
