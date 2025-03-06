<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request) {
        // Validations
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

        // Check if the user is over 18 years old
        $birthDate = new \DateTime($request->birthdate);
        $now = new \DateTime();
        $age = $now->diff($birthDate)->y;

        if ($age < 18) {
            return response()->json(['error' => 'You must be over 18 years old'], 403);
        }

        // Create user
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

        return response()->json(['message' => 'Registration successful', 'user' => $user], 201);
    }
}
