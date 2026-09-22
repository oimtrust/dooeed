<?php

namespace App\Http\Controllers\Api\Auth;

use App\Domains\Auth\Actions\AuthenticateUser;
use App\Domains\Auth\Actions\LogoutUser;
use App\Domains\Auth\Actions\RegisterUser;
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
    public function register(RegisterRequest $request, RegisterUser $registerUser): JsonResponse
    {
        $user = $registerUser->execute($request->validated());
        $this->startBrowserSession($request, $user);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
                'token_type' => 'Bearer',
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

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    public function logout(Request $request, LogoutUser $logoutUser): JsonResponse
    {
        $logoutUser->execute($request->user());

        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
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
