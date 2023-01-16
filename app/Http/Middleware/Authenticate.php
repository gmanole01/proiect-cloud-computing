<?php

namespace App\Http\Middleware;

use Closure;

class Authenticate
{
    public function handle($request, Closure $next, ...$guards)
    {

        $user = auth()->user();
        if(is_object($user)) {
            return $next($request);
        }

        return redirect()->route('login');
    }
}
