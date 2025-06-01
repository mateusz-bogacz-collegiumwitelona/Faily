<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CheckUserBanned;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            CheckUserBanned::class,
        ]);

        $middleware->alias([
            'locale' => setLocale::class,
            'admin' =>  AdminMiddleware::class,
            'banned' => CheckUserBanned::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
    })->create();
