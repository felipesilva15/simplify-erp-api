<?php

namespace App\Modules\Security\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtFromCookie
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->remove('Authorization');
        $token = $request->cookie(config('jwt.cookie_name')) ?? '';

        if ($token) {
            $request->headers->set(
                'Authorization',
                'Bearer ' . $token
            );
        }

        return $next($request);
    }
}
