<?php

namespace App\Http\Controllers;

use App\Models\RestrictedUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RestrictedUserController extends Controller
{
    // Mostrar todos los usuarios restringidos
    public function index()
    {
        $users = RestrictedUser::all();

        // Generar la URL completa para cada avatar
        $users = $users->map(function ($user) {
            $user->avatar_url = asset('storage/' . $user->avatar);
            return $user;
        });
    
        return response()->json($users);
    }

    // Mostrar un usuario restringido por ID
    public function show($id)
    {
        $user = RestrictedUser::findOrFail($id);
        return response()->json($user);
    }

    // Crear un nuevo usuario restringido
    public function store(Request $request)
    {
        // Validación de los campos
    $request->validate([
        'fullname' => 'required|string|max:255',
        'pin' => 'required|numeric|digits:6', // El PIN debe ser de 6 dígitos
        'avatar' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048', // Validación del avatar
    ]);

    // Guardar la imagen del avatar en 'storage/app/public/avatars'
    $path = $request->file('avatar')->store('avatars', 'public');

    // Crear el usuario restringido
    $user = RestrictedUser::create([
        'fullname' => $request->fullname,
        'pin' => $request->pin, // Almacenar el PIN directamente como número entero
        'avatar' => $path, // Ruta del avatar
        'user_id' => auth()->id(), // Asignar el usuario autenticado
    ]);

    return response()->json([
        'message' => 'Usuario restringido creado con éxito',
        'user' => $user
    ], 201);
    }

    public function update(Request $request, $id)
    {
        try {
            // Buscar el usuario
            $user = RestrictedUser::findOrFail($id);
            \Log::info('Usuario encontrado:', $user->toArray());
    
            // Logs para depuración
            \Log::info('Datos recibidos:', $request->all());
            \Log::info('Archivo avatar recibido:', ['avatar' => $request->file('avatar')]);
    
            // Validar los datos recibidos
            $validatedData = $request->validate([
                'fullname' => 'nullable|string|max:255',
                'pin' => 'nullable|numeric|digits:6',
                'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            ]);
            \Log::info('Datos validados:', $validatedData);
    
            // Preparar los datos para actualizar
            $dataToUpdate = [];
    
            if ($request->filled('fullname')) {
                $dataToUpdate['fullname'] = $request->input('fullname');
            }
    
            if ($request->filled('pin')) {
                $dataToUpdate['pin'] = $request->input('pin');
            }
    
            if ($request->hasFile('avatar')) {
                \Log::info('Avatar presente:', ['hasFile' => $request->hasFile('avatar')]);
    
                // Eliminar la imagen anterior si existe
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
    
                // Guardar la nueva imagen
                $path = $request->file('avatar')->store('avatars', 'public');
                \Log::info('Nueva imagen guardada:', ['path' => $path]);
                $dataToUpdate['avatar'] = $path;
            }
    
            \Log::info('Datos a actualizar:', $dataToUpdate);
    
            // Actualizar los datos
            $updated = $user->update($dataToUpdate);
            \Log::info('Resultado de la actualización:', ['updated' => $updated]);
    
            if (!$updated) {
                throw new \Exception('No se pudieron guardar los cambios en la base de datos.');
            }
    
            return response()->json([
                'message' => 'Usuario restringido actualizado con éxito',
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error al actualizar el usuario:', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Hubo un error al actualizar el usuario restringido.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    // Eliminar un usuario restringido
    public function destroy($id)
    {
        $user = RestrictedUser::findOrFail($id);

        // Eliminar la imagen asociada si existe
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Eliminar el usuario
        $user->delete();

        return response()->json(['message' => 'Usuario restringido eliminado con éxito'], 200);
    }
}
