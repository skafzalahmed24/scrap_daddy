<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
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

        // Rate limit hit: short, friendly message instead of an exception dump.
        $this->renderable(function (ThrottleRequestsException $e, $request) {
            $message = 'Too many attempts. Please wait a minute and try again.';
            $headers = $e->getHeaders();

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 0,
                    'message' => $message,
                    'data' => ['retry_after' => (int) ($headers['Retry-After'] ?? 60)],
                ], 429, $headers);
            }

            return redirect()->back()->with('error', $message);
        });

        // API clients (mobile app / web fetch calls) never get stack traces or file paths.
        // Details stay in storage/logs; validation and auth errors keep their normal format.
        $this->renderable(function (Throwable $e, $request) {
            if (!($request->expectsJson() || $request->is('api/*'))) {
                return null;
            }
            if ($e instanceof ValidationException || $e instanceof AuthenticationException) {
                return null;
            }

            $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
            $message = match (true) {
                $status === 404 => 'Not found.',
                $status === 403 => 'You are not allowed to do this.',
                $status === 419 => 'Your session expired. Please refresh and try again.',
                $status === 405 => 'This action is not supported.',
                $status >= 500 => 'Something went wrong. Please try again.',
                default => $e->getMessage() ?: 'Request failed.',
            };

            return response()->json(['status' => 0, 'message' => $message], $status);
        });
    }
}
