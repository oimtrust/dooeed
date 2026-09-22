<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveUser
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_if($request->user()?->trashed(), 401);
        abort_if($request->user()?->status !== 'active', 403, 'Akun ditangguhkan');

        if ($request->hasSession() && $request->session()->exists('admin_session_token')) {
            abort_unless($request->session()->get('admin_session_token') === $request->user()->getRememberToken(), 401);
        }

        return $next($request);
    }
}
