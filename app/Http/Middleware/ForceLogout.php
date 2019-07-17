<?php

namespace App\Http\Middleware;

use Closure;

class ForceLogout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check() && $request->user()->force_logout) {
            auth()->logout();
        }

        return $next($request);
    }
}
