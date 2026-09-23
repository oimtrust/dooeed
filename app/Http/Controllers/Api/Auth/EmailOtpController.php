<?php

namespace App\Http\Controllers\Api\Auth;

use App\Domains\Auth\Actions\RequestEmailOtp;
use App\Domains\Auth\Actions\VerifyEmailOtp;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RequestEmailOtpRequest;
use App\Http\Requests\Auth\VerifyEmailOtpRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailOtpController extends Controller
{
    public function request(RequestEmailOtpRequest $request, RequestEmailOtp $requestOtp): JsonResponse
    {
        $sent = $requestOtp->execute($request->validated('email'));

        return response()->json(['message' => $sent ? 'OTP sent' : 'If email exists, OTP sent']);
    }

    public function verify(VerifyEmailOtpRequest $request, VerifyEmailOtp $verifyOtp): JsonResponse
    {
        $user = $verifyOtp->execute($request->validated('email'), $request->validated('code'));
        $this->startBrowserSession($request, $user);

        return response()->json([
            'message' => 'Email verified',
            'data' => [
                'user' => new UserResource($user),
                'token' => app('tymon.jwt')->fromUser($user),
                'token_type' => 'Bearer',
            ],
        ]);
    }

    private function startBrowserSession(Request $request, User $user): void
    {
        if ($request->hasSession()) {
            Auth::guard('web')->login($user);
            $request->session()->regenerate();
            $request->session()->put('password_hash_web', $user->getAuthPassword());
            $request->session()->put('admin_session_token', $user->getRememberToken());
        }
    }
}
