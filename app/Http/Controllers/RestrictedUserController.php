<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RestrictedUserController extends Controller
{
    // Obtener todos los usuarios restringidos
    public function index()
    {
        $user = auth()->user();

        // Obtener usuarios restringidos asociados al usuario principal
        $restrictedUsers = User::where('parent_id', $user->id)->get(['id', 'name', 'avatar']);

        return response()->json($restrictedUsers);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'pin' => 'required|digits:6',
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        // Crear el usuario restringido
        $restrictedUser = new User();
        $restrictedUser->name = $request->name;
        $restrictedUser->pin = $request->pin;
    
        // Subir el avatar
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $restrictedUser->avatar = $path;
        }
    
        $restrictedUser->save();
    
        return response()->json([
            'message' => 'Usuario restringido creado exitosamente.',
            'user' => $restrictedUser,
        ], 201);
    }
    // Actualizar un usuario restringido
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'pin' => 'nullable|digits:6', // Solo actualizar el PIN si se proporciona
            'avatar' => 'required|in:avatar1,avatar2,avatar3',
        ]);

        $restrictedUser = User::findOrFail($id);

        // Solo el usuario principal puede actualizar los usuarios restringidos
        if ($restrictedUser->parent_id !== auth()->user()->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $restrictedUser->name = $request->name;
        if ($request->has('pin')) {
            $restrictedUser->pin = Hash::make($request->pin);
        }
        $restrictedUser->avatar = $request->avatar;
        $restrictedUser->save();

        return response()->json(['message' => 'Usuario restringido actualizado exitosamente']);
    }

    // Eliminar un usuario restringido
    public function destroy($id)
    {
        $restrictedUser = User::findOrFail($id);

        // Solo el usuario principal puede eliminar a los usuarios restringidos
        if ($restrictedUser->parent_id !== auth()->user()->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $restrictedUser->delete();

        return response()->json(200);
    }
}
