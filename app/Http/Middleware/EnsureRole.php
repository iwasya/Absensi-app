<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasRoleAlias($roles)) {
            abort(403, 'Akun kamu tidak punya akses ke halaman ini.');
        }

        return $next($request);
    }
}
