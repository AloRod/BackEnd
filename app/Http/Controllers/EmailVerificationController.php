<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    private function checkURL($id, $hash) //verifica que la url sea valida
    {
        $user = User::find($id);

        if (!$user) {
            return ['message' => 'User not found', 'code' => 404];
        }

        if ($user->pending) {
            return ['message' => 'Invalid URL', 'code' => 410];
        }

        return null;
    }

    private function verifyEmail($id)
    {
        $user = User::find($id);

        $user->update([
            'status' => 'active'
        ]);
    }

    public function verify(Request $request, $id, $hash): JsonResponse
    {
        $error = $this->checkURL($id, $hash);
        if ($error) {
            return response()->json(['error' => $error['message']], $error['code']);
        }

        $this->verifyEmail($id);
        return response()->json(['message' => 'Account successfully activated'], 200);
    }
}
