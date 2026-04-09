<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'sk.ready' => \App\Http\Middleware\EnsureSkDataReady::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Ensure abort(500, ...) and other HttpException 5xx are reported to logs.
        $exceptions->stopIgnoring(HttpException::class);

        // Keep common noise out of logs.
        $exceptions->dontReport([
            NotFoundHttpException::class,
        ]);
    })->create();
