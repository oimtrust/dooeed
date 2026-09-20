<?php

namespace App\Http\Controllers\Api\Auth;

use App\Domains\Auth\Actions\ResetUserPassword;
use App\Domains\Auth\Actions\SendPasswordResetLink;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Http\JsonResponse;

class PasswordResetController extends Controller
{
    public function forgot(ForgotPasswordRequest $request, SendPasswordResetLink $sendLink): JsonResponse
    {
        $message = $sendLink->execute($request->validated('email'));

        return response()->json(['message' => $message]);
    }

    public function reset(ResetPasswordRequest $request, ResetUserPassword $resetPassword): JsonResponse
    {
        $resetPassword->execute($request->validated());

        return response()->json(['message' => __('passwords.reset')]);
    }
}
