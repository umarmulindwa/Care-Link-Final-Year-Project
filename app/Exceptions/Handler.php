<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class Handler extends ExceptionHandler
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
            //
        });
    }

    // api protested routes
    protected function unauthenticated($request, Throwable $exception)
    {
        // return response()->json(['error' => 'Unauthenticated.'], 401);

        if ($exception instanceof AuthenticationException || $exception instanceof UnauthorizedHttpException) {
            if ($request->is('api/*')) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }

            // For web routes
            return redirect()->guest(route('login'));
        }

        return parent::render($request, $exception);
    }
}
