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
        // O cookie tem precedência absoluta: qualquer Authorization recebido
        // do cliente (header bag OU variável de servidor, usada como fallback
        // pelo parser do tymon/jwt-auth) é descartado e reescrito a partir do
        // cookie configurado em jwt.cookie_name.
        $request->headers->remove('Authorization');
        $request->server->remove('HTTP_AUTHORIZATION');
        $request->server->remove('REDIRECT_HTTP_AUTHORIZATION');

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
