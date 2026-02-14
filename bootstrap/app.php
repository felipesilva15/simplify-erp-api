<?php

use App\Core\Exceptions\AccessDeniedHttpException as CustomAccessDeniedHttpException;
use App\Core\Exceptions\AuthenticationException as CustomAuthenticationException;
use App\Core\Exceptions\NotFoundHttpException as CustomNotFoundHttpException;
use App\Core\Exceptions\ValidationException as CustomValidationException;
use App\Modules\Security\Http\Middleware\JwtFromCookie;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('api', [
            JwtFromCookie::class,
        ]);

        $middleware->alias([
            'jwt.cookie' => JwtFromCookie::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            throw new CustomAccessDeniedHttpException();
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            throw new CustomNotFoundHttpException();
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            throw new CustomAuthenticationException();
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            throw new CustomValidationException($e->validator);
        });
    })->create();
