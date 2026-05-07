<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        if (auth()->user()->role !== 'customer') {
            abort(403, 'Access denied. Customers only.');
        }

        return $next($request);
    }
}
