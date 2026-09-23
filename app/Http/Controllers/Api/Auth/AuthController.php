<?php

namespace App\Http\Controllers\Api\Auth;

use App\Domains\Auth\Actions\AuthenticateUser;
use App\Domains\Auth\Actions\LogoutUser;
use App\Domains\Auth\Actions\RegisterUser;
use App\Domains\Auth\Actions\RequestEmailOtp;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterUser $registerUser, RequestEmailOtp $requestEmailOtp): JsonResponse
    {
        $user = $registerUser->execute($request->validated());
        $requestEmailOtp->execute($user->email);

        return response()->json([
            'message' => 'OTP sent',
            'data' => [
                'user' => new UserResource($user),
            ],
        ], 201);
    }

    public function login(LoginRequest $request, AuthenticateUser $authenticateUser): JsonResponse
    {
        $user = $authenticateUser->execute(
            $request->validated('email'),
            $request->validated('password'),
        );

        $this->startBrowserSession($request, $user);

        return $this->tokenResponse($user);
    }

    public function refresh(): JsonResponse
    {
        $jwt = Auth::guard('api');
        $token = $jwt->refresh();

        // The presented token has been blacklisted, so drop it before anything
        // else in this request asks the guard who the current user is.
        $jwt->unsetToken();

        return response()->json([
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    public function logout(LogoutUser $logoutUser): JsonResponse
    {
        $logoutUser->execute();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    /**
     * Issue a signed JWT for the user alongside their resource.
     *
     * The token is minted straight from the JWT factory instead of through
     * Auth::guard('api')->login(), so issuing it does not also mark the user as
     * authenticated for the remainder of this request.
     */
    private function tokenResponse(User $user, int $status = 200): JsonResponse
    {
        return response()->json([
            'data' => [
                'user' => new UserResource($user),
                'token' => app('tymon.jwt')->fromUser($user),
                'token_type' => 'Bearer',
            ],
        ], $status);
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
