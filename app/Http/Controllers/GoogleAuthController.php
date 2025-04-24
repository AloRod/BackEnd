<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GoogleAuthController extends Controller
{
    private function createGoogleUser($request)
    {
        // Create user
        return User::create([
            'email' => $request->email,
            'password' => Hash::make('default'),
            'name' => $request->name,
            'lastname' => $request->lastname,
            'status' => 'pending',
        ]);
    }

    private function generateToken($user)
    {
        return JWTAuth::fromUser($user);
    }

//funcion para buscar si el usuario existe o no
    public function handleGoogleAuth(Request $request)
    {
        // Validations
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'name' => 'required',
            'lastname' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Check if the user exists
            $user = User::where('email', $request->email)->first();
            $googleUser = $user ?: $this->createGoogleUser($request);

            // Generate token and return user data
            $token = $this->generateToken($googleUser);

            $needs_completion = $user ? $user->status == 'pending' : true; //identifica si es un usuario nuevo

            return response()->json([
                'message' => 'Welcome',
                'user' => [
                    'id' => $googleUser->id,
                    'email' => $googleUser->email,
                    'name' => $googleUser->name,
                ],
                'token' => $token,
                'needs_completion' => $needs_completion,
                'expires_in' => auth('api')->factory()->getTTL() * 60
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to authenticate with Google: ' . $e->getMessage()], 500);
        }
    }

    public function completeGoogleProfile(Request $request)
    {

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'lastname' => 'string',
            'phone' => 'required|unique:users',
            'pin' => 'required|digits:6',
            'country' => 'required',
            'birthdate' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if the user is over 18 years old
        $birthDate = new \DateTime($request->birthdate);
        $now = new \DateTime();
        $age = $now->diff($birthDate)->y;

        if ($age < 18) {
            return response()->json(['errors' => ['birthdate' => 'You must be over 18 years old']], 403);
        }

        // Create the user

        $user = auth('api')->user();

        $user->update([
            'name' => $request->name,
            'lastname' => $request->lastname,
            'phone' => $request->phone,
            'pin' => $request->pin,
            'country' => $request->country,
            'birthdate' => $request->birthdate,
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'Registration successful',
        ], 200);
    }
}
