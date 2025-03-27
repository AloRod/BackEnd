<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\User;
use App\Models\RestrictedUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    // Home screen
    public function index()
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }
    
        $user = Auth::user();
    
        // Get restricted users associated with the administrator
        $restrictedUsers = RestrictedUser::where('user_id', $user->id)->get([
            'id',
            'fullname',
            'avatar'
        ]);
    
        // Add the avatar_url property to each user
        $restrictedUsers->transform(function ($user) {
            $user->avatar_url = $user->avatar ? asset('storage/' . $user->avatar) : null;
            return $user;
        });
    
        return response()->json([
            'message' => 'Successful login',
            'user' => $user,
            'restricted_users' => $restrictedUsers
        ]);
    }

    // Validate PIN for admin access
    public function validateAdminPin(Request $request)
    {
        // Check if the user is authenticated
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }
    
        // Check if the PIN is present in the request
        if (!$request->has('pin')) {
            return response()->json(['error' => 'PIN is required'], 400);
        }
    
        // Check if the PIN is correct
        if (!hash_equals($user->pin, $request->pin)) {
            return response()->json(['error' => 'Incorrect PIN'], 403);
        }
    
        return response()->json(['message' => 'Access granted to administration'], 200);
    }

    // Validate restricted user's PIN to view playlist
    public function validateRestrictedUserPin(Request $request)
    {
        // Validar que el PIN esté presente en la solicitud
        $request->validate([
            'pin' => 'required|string', // Asegúrate de que el PIN sea una cadena
        ]);
    
        // Buscar al usuario restringido por su PIN
        $user = RestrictedUser::where('pin', $request->pin)->first();
    
        if (!$user) {
            return response()->json(['error' => 'Incorrect PIN'], 403);
        }
    
        // Obtener las playlists asociadas al usuario usando whereJsonContains
        $playlists = Playlist::whereJsonContains('associated_profiles', $user->id)
            ->withCount('videos') // Contar los videos asociados a cada playlist
            ->get()
            ->map(function ($playlist) {
                return [
                    'id' => $playlist->id,
                    'name' => $playlist->name,
                    'video_count' => $playlist->videos_count,
                ];
            });
    
        return response()->json([
            'message' => 'Access granted',
            'user' => [
                'id' => $user->id,
                'fullname' => $user->fullname,
            ],
            'playlists' => $playlists,
        ], 200);
    }
}