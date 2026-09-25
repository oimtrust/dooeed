<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdatePreferencesRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json(['data' => ['name' => $user->name, 'email' => $user->email, 'preferred_locale' => $user->preferred_locale, 'preferred_currency' => $user->preferred_currency, 'currency_example' => money(0, $user->preferred_currency)->format()]]);
    }

    public function updatePreferences(UpdatePreferencesRequest $request): JsonResponse
    {
        $request->user()->update($request->validated());
        app()->setLocale($request->validated('preferred_locale'));

        return $this->show($request);
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $request->user()->forceFill(['password' => $request->validated('password'), 'remember_token' => Str::random(60)])->save();

        return response()->json(['message' => 'Password updated successfully.']);
    }
}
