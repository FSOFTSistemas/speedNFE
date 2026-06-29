<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Psr\Log\LogLevel;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
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
        //
    }

    public function render($request, Throwable $e)
    {
        if ($this->shouldReturnJson($request)) {
            return $this->renderApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($this->shouldReturnJson($request)) {
            return response()->json([
                'message' => 'Nao autenticado.',
            ], 401);
        }

        return parent::unauthenticated($request, $exception);
    }

    private function renderApiException(Request $request, Throwable $e): JsonResponse
    {
        if ($e instanceof ValidationException) {
            return response()->json([
                'message' => 'Erro de validacao.',
                'errors' => $e->errors(),
            ], 422);
        }

        if ($e instanceof AuthenticationException) {
            return response()->json([
                'message' => 'Nao autenticado.',
            ], 401);
        }

        if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            return response()->json([
                'message' => 'Recurso nao encontrado.',
            ], 404);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'message' => 'Metodo nao permitido para este endpoint.',
            ], 405);
        }

        $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
        $message = $status >= 500 ? 'Erro interno no servidor.' : ($e->getMessage() ?: 'Erro na requisicao.');

        $payload = [
            'message' => $message,
        ];

        if (config('app.debug')) {
            $payload['exception'] = class_basename($e);
            $payload['error'] = $e->getMessage();
        }

        return response()->json($payload, $status);
    }

    protected function shouldReturnJson($request, ?Throwable $e = null): bool
    {
        return $request->is('api/*') || $request->expectsJson();
    }
}
