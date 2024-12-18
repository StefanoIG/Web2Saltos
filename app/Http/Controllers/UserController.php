<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Negocio;
use App\Models\Cita;

class UserController extends Controller
{
    public function actualizarNombre(Request $request, $id){
        $request->validate([
            'name'=> 'required|string|max:255'
        ]);

        $usuario = User::findOrFail($id);
        $usuario->update($request->all());

        return response()->json(['message' => 'Nombre actualizado correctamente']);
    }

    public function actualizarNumero(Request $request, $id){
        $request->validate([
            'numero'=> 'required|digits:10'
        ]);

        $usuario = User::findOrFail($id);
        $usuario->update($request->all());

        return response()->json(['message' => 'Numero telefonico actualizado correctamente']);
    }

    public function index(){
        return response()->json(User::all());
    }
 
    public function actualizarRol(Request $request, $id){
         $user = User::find($id);
 
         if (!$user) {
             return response()->json(['message' => 'Usuario no encontrado'], 404);
         }

         if ($user->role === 'adminglobal') {
            return response()->json(['message' => 'No se puede cambiar el rol del administrador global.'], 403);
        }
 
         $request->validate([
             'role' => 'required|in:administrador,usuario,adminglobal',
         ]);
 
         $user->role = $request->role;
         $user->save();
 
         return response()->json(['message' => 'Rol actualizado con éxito', 'user' => $user]);
    }
 
    public function eliminar($id){
         $user = User::find($id);
 
         if (!$user) {
             return response()->json(['message' => 'Usuario no encontrado'], 404);
    }


    if ($user->role === 'adminglobal') {
        return response()->json(['message' => 'No se puede eliminar al administrador global.'], 403);
    }
 
         $user->delete();
 
         return response()->json(['message' => 'Usuario eliminado con éxito']);
    }
}
