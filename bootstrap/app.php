<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,

        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\CheckRole::class.':admin',
            'author' => \App\Http\Middleware\CheckRole::class.':author',
            'school' => \App\Http\Middleware\CheckRole::class.':school',
            'student' => \App\Http\Middleware\CheckRole::class.':student',
            'teacher' => \App\Http\Middleware\CheckRole::class.':teacher',
            'consumer' => \App\Http\Middleware\EnsureUserIsConsumer::class.':consumer',
            'reader' => \App\Http\Middleware\CheckRole::class.':reader,author,student,school',
            'adult_access' => \App\Http\Middleware\AdultAccessMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
