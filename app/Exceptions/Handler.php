<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

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
        $this->reportable(function (\Throwable $e) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        });

        $this->renderable(function (ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Resource not found'], 404);
            }
        });

        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Route not found'], 404);
            }
        });

        $this->renderable(function (AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized action'], 403);
            }
        });

        $this->renderable(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });
    }

    public function report(\Throwable $e): void
    {
        if ($this->shouldReport($e)) {
            Log::error('Exception occurred: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        parent::report($e);
    }
}
