<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        $allowed = collect($roles)
            ->flatMap(fn($r) => explode(',', $r))
            ->map('trim')
            ->toArray();

        if (!in_array(auth()->user()->role, $allowed)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
