<?php

namespace App\Http\Controllers\Api\Auth;

use App\Domains\Auth\Actions\RequestEmailOtp;
use App\Domains\Auth\Actions\VerifyEmailOtp;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RequestEmailOtpRequest;
use App\Http\Requests\Auth\VerifyEmailOtpRequest;
use Illuminate\Http\JsonResponse;

class EmailOtpController extends Controller
{
    public function request(RequestEmailOtpRequest $request, RequestEmailOtp $requestOtp): JsonResponse
    {
        $sent = $requestOtp->execute($request->validated('email'));

        return response()->json(['message' => $sent ? 'OTP sent' : 'If email exists, OTP sent']);
    }

    public function verify(VerifyEmailOtpRequest $request, VerifyEmailOtp $verifyOtp): JsonResponse
    {
        $verifyOtp->execute($request->validated('email'), $request->validated('code'));

        return response()->json(['message' => 'Email verified']);
    }
}
