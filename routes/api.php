<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NegocioController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CitaConfirmacionController;

//Rutas publicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

//Rutas para todos los usuarios autenticados
Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'user']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

//Rutas para los usuarios
Route::middleware('auth:sanctum')->get('/negocios', [NegocioController::class, 'index']);
Route::middleware('auth:sanctum')->get('/negocio/{id_negocio}', [NegocioController::class, 'show']);
Route::middleware('auth:sanctum')->post('/agendar-cita', [CitaController::class, 'agendarCita']);
Route::middleware('auth:sanctum')->get('/citas/user/{userid}', [CitaController::class, 'obtenerCitasUsuario']);
Route::middleware('auth:sanctum')->get('/citas/{id_cita}', [CitaController::class, 'show']);
Route::middleware('auth:sanctum')->put('/citas/{id_cita}/descripcion', [CitaController::class, 'actualizarDescripcion']);
Route::middleware('auth:sanctum')->put('/citas/{id_cita}/cancelar', [CitaController::class, 'cancelarCita']);
Route::middleware('auth:sanctum')->put('/usuario/actualizar/{id}/nombre', [UserController::class, 'actualizarNombre']);
Route::middleware('auth:sanctum')->put('/usuario/actualizar/{id}/numero', [UserController::class, 'actualizarNumero']);

//Rutas para administador
Route::middleware(['auth:sanctum', 'role:administrador'])->group(function () {
    Route::get('/negocios/admin/{userid}', [NegocioController::class, 'getNegociosPorUsuario']);
    Route::post('/negocios/admin/crear', [NegocioController::class, 'store']);
    Route::delete('/negocios/admin/{id}', [NegocioController::class, 'borrarNegocio']);
    Route::put('/negocios/admin/{id}', [NegocioController::class, 'update']);
    Route::put('/servicios/admin/{id}', [ServicioController::class, 'update']);
    Route::put('/horarios/admin/{id}', [HorarioController::class, 'update']);
    Route::post('/servicios/admin/{negocioId}/crear', [ServicioController::class, 'store']);
    Route::delete('/servicios/admin/{servicioId}/borrar', [ServicioController::class, 'destroy']);
    Route::get('/negocios/admin/{id}/horarios/verificar/{dia}', [NegocioController::class, 'verificarDiaExistente']);
    Route::post('/negocios/admin/{id}/horarios/crear', [NegocioController::class, 'crearHorario']);
    Route::delete('/negocios/admin/{id}/horarios/borrar', [HorarioController::class, 'destroy']);
    Route::get('/negocios/admin/citas/{userid}', [NegocioController::class, 'getCitasPorEstado']);
    Route::get('/negocios/admin/{idNegocio}/citas', [NegocioController::class, 'getCitasPorNegocio']);
    Route::put('/negocios/admin/citas/{idCita}', [CitaController::class, 'updateCita']);
    Route::put('/negocios/admin/citas/{idCita}/cancelar', [CitaController::class, 'cancelCita']);
    Route::put('/negocios/admin/citas/{idCita}/expirar', [CitaController::class, 'expireCita']);
    Route::post('/negocios/admin/citas/{id}/confirmar', [CitaConfirmacionController::class, 'confirmar']);
});

//Rutas para admin global
Route::middleware(['auth:sanctum', 'role:adminglobal'])->group(function () {
Route::get('/usuarios', [UserController::class, 'index']);
Route::put('/usuarios/{id}/rol', [UserController::class, 'actualizarRol']);
Route::delete('/usuarios/{id}', [UserController::class, 'eliminar']);
});


