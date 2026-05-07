<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StaffMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $user = auth()->user();

        if (! in_array($user->role, ['admin', 'staff'])) {
            abort(403, 'Access denied.');
        }

        if ($user->role === 'staff' && $user->status !== 'active') {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Your account is not active. Please contact the administrator.');
        }

        return $next($request);
    }
}
