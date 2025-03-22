<?php

namespace App\Http\Controllers;

use App\Models\RestrictedUser;
use Illuminate\Http\Request;

class RestrictedUserController extends Controller
{
    // Mostrar todos los usuarios restringidos
    public function index()
    {
        $users = RestrictedUser::all();
        return response()->json($users);
    }

    // Mostrar un usuario restringido por ID
    public function show($id)
    {
        $user = RestrictedUser::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario restringido no encontrado'], 404);
        }

        return response()->json($user);
    }

    // Crear un nuevo usuario restringido
    public function store(Request $request)
    {
        $user_id = auth()->user()->id;
        // Validación de los campos
        $request->validate([
            'fullname' => 'required|string|max:255',
            'pin' => 'required|numeric|digits:6', // Validación para el PIN
            'avatar' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048', // Asegura que el avatar sea una imagen válida
            'user_id' => 'required|exists:users,id', // Validación para que el user_id exista en la tabla users
        ]);

        // Guardar la imagen del avatar en el directorio 'avatars' dentro de 'storage/app/public'
        $path = $request->file('avatar')->store('avatars', 'public');

        // Crear el nuevo usuario restringido
        $user = RestrictedUser::create([
            'fullname' => $request->fullname,
            'pin' => $request->pin, // Encriptar el PIN
            'avatar' => $path, // Guardar la ruta del archivo de imagen
            'user_id' => auth()->id(),
        ]);

        return response()->json($user, 201);
    }

   // app/Http/Controllers/RestrictedUserController.php

public function update(Request $request, $id)
{
    $user = RestrictedUser::find($id);

    if (!$user) {
        return response()->json(['message' => 'Usuario restringido no encontrado'], 404);
    }

    // Validaciones
    $request->validate([
        'fullname' => 'required|string|max:255',
        'pin' => 'nullable|numeric|digits:4', // El PIN es opcional al actualizar
        'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // El avatar es opcional al actualizar
        'user_id' => 'required|exists:users,id', // Validación de que el user_id existe
    ]);

    // Solo actualizamos los campos que están presentes en la solicitud
    if ($request->has('pin')) {
        $user->pin = bcrypt($request->pin); // Encriptamos el nuevo PIN
    }

    if ($request->has('avatar')) {
        $user->avatar = $request->avatar; // Actualizamos el avatar si se envió
    }

    // Actualizar otros campos
    $user->fullname = $request->fullname;
    $user->user_id = $request->user_id;

    // Guardamos los cambios
    $user->save();

    return response()->json($user, 200);
}

    
    // Eliminar un usuario restringido
    public function destroy($id)
    {
        // Buscar el usuario restringido por ID
        $user = RestrictedUser::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario restringido no encontrado'], 404);
        }

        // Eliminar el usuario restringido
        $user->delete();
        return response()->json(['message' => 'Usuario restringido eliminado con éxito']);
    }
}
