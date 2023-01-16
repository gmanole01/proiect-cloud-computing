<?php

namespace App\Http\Middleware;

use Closure;

class NoAuthenticate
{
    public function handle($request, Closure $next)
    {

        $user = auth()->user();
        if(is_object($user)) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
