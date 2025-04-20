<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client;

class TwoFactorAuthService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function generateVerificationCode()
    {
        // Generate a 6-digit code
        return rand(100000, 999999);
    }

    public function sendSmsVerification($phoneNumber, $code)
    {
        // Implement your SMS sending logic here
        // Example using a service like Twilio
        try {
            // You'll need to install the Twilio SDK: composer require twilio/sdk
            $sid = env('TWILIO_SID');
            $token = env('TWILIO_AUTH_TOKEN');
            $twilioNumber = env('TWILIO_PHONE_NUMBER');

            $twilio = new Client($sid, $token);
            $twilio->messages->create(
                "+506$phoneNumber",
                [
                    'from' => $twilioNumber,
                    'body' => "Your KidsYT verification code is: $code"
                ]
            );

            // Log successful SMS sending
            \Log::info("SMS verification sent to $phoneNumber");
        } catch (\Exception $e) {
            // Log any errors
            \Log::error("Failed to send SMS: " . $e->getMessage());
            throw $e;
        }
    }

    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'code' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $storedCode = Cache::get('sms_code_' . $request->user_id);
        $user = User::findOrFail($request->user_id);

        if (!$storedCode || $storedCode != $request->code) {
            return response()->json(['error' => 'Invalid or expired verification code'], 401);
        }

        // Code is valid, delete it from cache
        Cache::forget('sms_code_' . $request->user_id);

        // Create a token for the user
        $token = $user->createToken('auth_token', ['*'], now()->addDays(30))->plainTextToken;

        // Respond with the user data and token
        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
            ],
            'token' => $token
        ], 200);
    }
}
