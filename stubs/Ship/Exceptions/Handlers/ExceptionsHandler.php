<?php

namespace App\Ship\Exceptions\Handlers;

use Illuminate\Foundation\Exceptions\Handler as LaravelExceptionHandler;
use Throwable;

class ExceptionsHandler extends LaravelExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if (app()->bound('sentry') && ! app()->environment('local')) {
                app('sentry')->captureException($e);
            }
        });
    }
}
