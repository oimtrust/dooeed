<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        Auth::guard('web')->login($request->user());
        $request->session()->regenerate();
        $request->session()->put('admin_session_token', $request->user()->getRememberToken());
        $request->session()->put('password_hash_web', $request->user()->getAuthPassword());

        return response()->json(['redirect' => route('admin.users.index')]);
    }

    public function destroy(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Sesi diakhiri.']);
    }
}
