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
        $middleware->validateCsrfTokens(except: [
            'payment/callback/*',
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\SetCurrency::class,
            \App\Http\Middleware\CheckAdultAccessExpiration::class,
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\CheckRole::class.':admin',
            'author' => \App\Http\Middleware\CheckRole::class.':author',
            'school' => \App\Http\Middleware\CheckRole::class.':school',
            'student' => \App\Http\Middleware\CheckRole::class.':student',
            'teacher' => \App\Http\Middleware\CheckRole::class.':teacher',
            'parent' => \App\Http\Middleware\CheckRole::class.':parent',
            'consumer' => \App\Http\Middleware\EnsureUserIsConsumer::class.':consumer',
            'reader' => \App\Http\Middleware\CheckRole::class.':reader,author,student,school',
            'adult_access' => \App\Http\Middleware\AdultAccessMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (Throwable $e) {
            try {
                $admins = \App\Models\User::where('role', 'admin')->get();
                $notificationService = app(\App\Services\NotificationService::class);

                foreach ($admins as $admin) {
                    $notificationService->sendNotification(
                        $admin,
                        'System Error Occurred',
                        'An error occurred: ' . $e->getMessage(),
                        null,
                        'error',
                        true
                    );
                }
            } catch (\Throwable $notificationError) {
                \Illuminate\Support\Facades\Log::error('Failed to send error notification to admins: ' . $notificationError->getMessage());
            }
        });
    })->create();
