<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use App\Services\TwoFactorAuthService;

class LoginController extends Controller
{
    protected $tfaService;

    public function __construct(TwoFactorAuthService $tfaService)
    {
        $this->tfaService = $tfaService;
    }
    public function login(Request $request)
    {
        // Validations
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',  // Validates that the email is present and valid
            'password' => 'required|min:8',  // Validates that the password is present and has at least 8 characters
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);  // Returns errors if validation fails
        }

        // Check if the user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not registered'], 404);  // Returns an error if the user does not exist
        }

        // Verifying the password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);  // If the password doesn't match
        }

        if ($user->status == 'pending') {
            return response()->json(
                ['error' => 'Your account is not yet active. Please check your email and follow the instructions to activate it.', 'data' => $user],
                403
            );
        }

        // Create a token for the user
        $token = $user->createToken('auth_token', ['*'], now()->addDays(30))->plainTextToken;
        if ($user->phone) {

            $verificationCode = $this->tfaService->generateVerificationCode();

            Cache::put('sms_code_' . $user->id, $verificationCode, now()->addMinutes(10));

            $this->tfaService->sendSmsVerification($user->phone, $verificationCode);
        }

       return response()->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id, // Returning the user ID
                'email' => $user->email,
                'name' => $user->name,
            ],
            'token' => $token,
            'requires_verification' => true
        ], 200);
    }
}



