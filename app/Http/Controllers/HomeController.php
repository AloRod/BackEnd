<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RestrictedUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    // Pantalla de inicio
    public function index()
    {
        $user = Auth::user();

        if (!Auth::guard('sanctum')->check()) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        // Obtener usuarios restringidos asociados al administrador
        $restrictedUsers = RestrictedUser::where('user_id', $user->id)->get(['id', 'fullname', 'avatar']);

        return response()->json([
            'message' => 'Inicio exitoso',
            'user' => $user,
            'restricted_users' => $restrictedUsers
        ]);
    }

    // Validar PIN para ingresar a la administración
    public function validateAdminPin(Request $request)
    {
        // Verificar si el usuario está autenticado
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'No autenticado'], 401);
        }
    
        // Verificar si el PIN está presente en la solicitud
        if (!$request->has('pin')) {
            return response()->json(['error' => 'El PIN es obligatorio'], 400);
        }
    
        // Verificar si el PIN es correcto
        if (!hash_equals($user->pin, $request->pin)) {
            return response()->json(['error' => 'PIN incorrecto'], 403);
        }
    
        return response()->json(['message' => 'Acceso permitido a administración'], 200);
    }

    //Validar PIN del usuario restringido para ver playlist
    public function validateRestrictedUserPin(Request $request, $id)
    {
        $restrictedUser = RestrictedUser::find($id);

        if (!$restrictedUser || !Hash::check($request->pin, $restrictedUser->pin)) {
            return response()->json(['error' => 'PIN incorrecto'], 403);
        }

        return response()->json(['message' => 'Acceso permitido a playlist', 'user' => $restrictedUser]);
    }
}
