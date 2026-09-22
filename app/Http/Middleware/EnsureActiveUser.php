<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_if($user?->trashed(), 401);
        abort_if($user?->status !== 'active', 403, 'Akun ditangguhkan');

        $this->rejectStaleJwt($user);

        if ($request->hasSession() && $request->session()->exists('admin_session_token')) {
            abort_unless($request->session()->get('admin_session_token') === $request->user()->getRememberToken(), 401);
        }

        return $next($request);
    }

    /**
     * Reject a bearer token issued before the user's password changed.
     *
     * JWTs are stateless, so a password reset invalidates outstanding tokens by
     * comparing the fingerprint carried in the token against the stored hash.
     */
    private function rejectStaleJwt(?User $user): void
    {
        $jwt = Auth::guard('api');

        if (! $jwt->check()) {
            return;
        }

        $payload = $jwt->payload();

        if ($payload->get('sub') === $user?->getKey()) {
            abort_unless($payload->get('pwv') === $user?->passwordVersion(), 401);
        }
    }
}
