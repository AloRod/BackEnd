<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class RegisterController extends Controller
{
    public function register(Request $request) {
        // Validaciones
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
            'phone' => 'required|unique:users',
            'pin' => 'required|digits:6',
            'name' => 'required',
            'lastname' => 'required',
            'birthdate' => 'required|date',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // Verifica si es mayor de 18 años
        $birthDate = new \DateTime($request->birthdate);
        $now = new \DateTime();
        $age = $now->diff($birthDate)->y;
    
        if ($age < 18) {
            return response()->json(['error' => 'Debes ser mayor de 18 años'], 403);
        }
    
        // Crear usuario
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'pin' => $request->pin,
            'name' => $request->name,
            'lastname' => $request->lastname,
            'country' => $request->country,
            'birthdate' => $request->birthdate,
            'status' => $request->status ?? 'pending',
            'verification_token' => bin2hex(random_bytes(16)),
        ]);
    
        return response()->json(['message' => 'Registro exitoso', 'user' => $user], 201);
    }
    
}
