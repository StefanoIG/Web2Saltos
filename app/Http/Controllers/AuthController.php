<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'cedula' => 'required|digits:10|unique:users,cedula',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'numero' =>'required|digits:10|unique:users,numero',
            'password' => 'required|string|min:8',
        ], [
            'cedula.required' => 'La cédula es obligatoria.',
            'cedula.digits' => 'La cédula debe tener exactamente 10 dígitos.',
            'cedula.unique' => 'Esta cédula ya está registrada.',
            'email.unique' => 'Este correo ya está registrado.',
            'numero.unique' => 'Este numero telefonico ya esta registrado',
        ]);
        
        $user = User::create([
            'cedula' => $request->cedula,
            'name' => $request->name,
            'email' => $request->email,
            'numero' =>$request->numero,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['user' => $user], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);
    
        $user = \App\Models\User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Este correo electrónico no está registrado.'], 404);
        }
    
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'La contraseña es incorrecta.'], 401);
        }
    
        $user = Auth::user();
    
        return response()->json([
            'token' => $user->createToken('API Token')->plainTextToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ], 200);
    }
    
    public function user(Request $request)
    {
    if ($request->user()) {
        return response()->json($request->user());
    } else {
        return response()->json(['message' => 'Usuario no autenticado'], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}

