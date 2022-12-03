<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
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

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Exception|Throwable $exception
     * @return void
     * @throws Throwable
     */
    public function report(Exception|Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param Exception|Throwable $exception
     * @return \Illuminate\Http\JsonResponse
     * @throws Throwable
     */
    public function render($request, Exception|Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            return response()->json(['error' => 'Data not found.'], 404);
        } elseif ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json(['error' => 'Method not allowed.'], 405);
        } elseif ($exception instanceof VoucherIsUsed) {
            return response()->json(['error' => 'This vouches is used already.'], 400);
        } elseif ($exception instanceof VoucherIsExpired) {
            return response()->json(['error' => 'This voucher is expired.'], 400);
        } elseif ($exception instanceof VoucherIsNotFound) {
            return response()->json(['error' => 'This voucher doesn\'t exist.'], 400);
        }

        return parent::render($request, $exception);
    }
}
