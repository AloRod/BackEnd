<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateRestrictedUserRequest;
use App\Models\RestrictedUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RestrictedUserController extends Controller
{
    // Display all restricted users
    public function index()
    {
        $users = RestrictedUser::all();

        // Generate the full URL for each avatar
        $users = $users->map(function ($user) {
            $user->avatar_url = asset('storage/' . $user->avatar);
            return $user;
        });

        return response()->json($users);
    }

    // Show a restricted user by ID
    public function show($id)
    {
        $user = RestrictedUser::findOrFail($id);
        return response()->json($user);
    }

    // Create a new restricted user
    public function store(Request $request)
    {
        // Validate fields
        $request->validate([
            'fullname' => 'required|string|max:255',
            'pin' => 'required|numeric|digits:6', // PIN must be 6 digits
            'avatar' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048', // Avatar validation
        ]);

        // Store the avatar image in 'storage/app/public/avatars'
        $path = $request->file('avatar')->store('avatars', 'public');

        // Create the restricted user
        $user = RestrictedUser::create([
            'fullname' => $request->fullname,
            'pin' => $request->pin, // Store PIN directly as an integer
            'avatar' => $path, // Avatar path
            'user_id' => auth()->id(), // Assign the authenticated user
        ]);

        return response()->json([
            'message' => 'Restricted user successfully created',
            'user' => $user
        ], 201);
    }

    // Update a restricted user
    public function update(Request $request, $id)
    {
        try {
            // Find the restricted user
            $user = RestrictedUser::find($id);

            // Check if the user exists
            if (!$user) {
                return response()->json(['message' => 'Restricted user not found'], 404);
            }

            // Update user data
            $user->update([
                'fullname' => $request->fullname,
                'pin' => $request->pin,
            ]);

            // Handle avatar update (if provided)
            if ($request->hasFile('avatar')) {
                // Delete the previous image if it exists
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }

                // Store the new image
                $path = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = $path;
                $user->save();
            }

            // Return JSON response with the updated user
            return response()->json([
                'message' => 'Restricted user successfully updated',
                'user' => $user,
            ], 200);

        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error updating restricted user:', ['error' => $e->getMessage()]);

            // Return JSON response with the error message
            return response()->json([
                'message' => 'An error occurred while updating the restricted user.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Delete a restricted user
    public function destroy($id)
    {
        $user = RestrictedUser::findOrFail($id);

        // Delete the associated image if it exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Delete the user
        $user->delete();

        return response()->json(['message' => 'Restricted user successfully deleted'], 200);
    }
}
