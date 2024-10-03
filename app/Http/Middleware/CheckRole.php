<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $status): Response
    {
        if($status == 'admin' && auth()->user()->status != 'admin' ) {
            abort(403);
        }
        if($status == 'user' && auth()->user()->status != 'user' ) {
            abort(403);
        }
        return $next($request);
    }
}
