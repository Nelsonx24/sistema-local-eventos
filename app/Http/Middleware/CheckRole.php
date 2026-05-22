<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = Auth::guard('staff')->user();

        if (! $user || ! in_array($user->role, $roles)) {
            Log::warning('Acceso denegado por rol', [
                'user' => $user?->name ?? 'Guest',
                'role' => $user?->role ?? 'N/A',
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'required_roles' => $roles,
            ]);

            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
