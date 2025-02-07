<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function invalid($request, ValidationException $exception)
    {
        // Custom response structure for validation errors
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $exception->errors()
        ], 422);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized access',
        ], 401);
    }

    public function render($request, Throwable $exception)
    {
        // Log detailed error information
        Log::error('Exception occurred: ', [
            'message' => $exception->getMessage(),
            'stack' => $exception->getTraceAsString(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user' => auth()->user() ? auth()->user()->id : 'Guest', // Log user ID if authenticated
        ]);

        return parent::render($request, $exception);
    }
}
