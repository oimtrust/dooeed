<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role !== 'admin') {
            return ($request->is('api/*') || $request->expectsJson())
                ? response()->json(['message' => 'Akses ditolak', 'redirect' => route('dashboard')], 403)
                : response()->view('admin.forbidden', [], 403);
        }

        return $next($request);
    }
}
