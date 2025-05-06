<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Services\MailtrapService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    protected $mailtrapService;

    public function __construct(MailtrapService $mailtrapService)
    {
        $this->mailtrapService = $mailtrapService;
    }
    private function getVerificationURL($user) //ruta que procesa el front y el backend
    {
        if (!method_exists($user, 'getEmailForVerification')) {
            Log::warning('User model does not have getEmailForVerification method. Falling back to ->email.');
            $emailForVerification = $user->email;
        } else {
            $emailForVerification = $user->getEmailForVerification();
        }

        $backendVerificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $user->getKey(),
                'hash' => sha1($emailForVerification),
            ]
        );


        $frontendUrl = config('app.frontend_url');
        if (!$frontendUrl) {
            Log::error('Frontend URL (APP_FRONTEND_URL) is not configured.');
            return response()->json(['message' => 'Registration successful, but verification email could not be prepared (frontend URL missing).', 'user' => $user], 201);
        }
        return rtrim($frontendUrl, '/') . '/verify-email' . '?verify_url=' . urlencode($backendVerificationUrl);
    }

    private function sendVerificationEmail($user)
    {
        try {
            $fullVerificationUrl = $this->getVerificationURL($user);

            $emailSent = $this->mailtrapService->sendVerificationEmail(
                $user->email,
                $user->name,
                $fullVerificationUrl
            );

            if (!$emailSent) {
                Log::error('Mailtrap verification email failed to send for user.', ['user_id' => $user->id, 'email' => $user->email]);
                return response()->json(['message' => 'Registration successful, but failed to send verification email.', 'user' => $user], 207);
            }

        } catch (\Exception $e) {
            Log::error('Error generating verification URL or sending email.', [
                'user_id' => $user->id,
                'exception' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Registration successful, but an error occurred while sending the verification email.', 'user' => $user], 201);
        }
    }
    public function register(Request $request)
    {
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
            'country' => 'required',
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
            'status' => 'pending', // Estado inicial siempre "pending"
        ]);

        $this->sendVerificationEmail($user);

        return response()->json(['message' => 'Registration successful', 'user' => $user], 201);

    }

}
