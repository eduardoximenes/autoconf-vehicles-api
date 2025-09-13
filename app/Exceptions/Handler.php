<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (Throwable $e, Request $request) {
            // Debug: sempre intercepta rotas da API
            if ($request->is('api/*')) {
                switch (true) {
                    case $e instanceof ValidationException:
                        /** @var \Illuminate\Validation\ValidationException $e */
                        return $this->error($e->errors(), 'Os dados fornecidos são inválidos', Response::HTTP_UNPROCESSABLE_ENTITY);

                    case $e instanceof ModelNotFoundException:
                        return $this->error([], 'Recurso não encontrado', Response::HTTP_NOT_FOUND);

                    case $e instanceof NotFoundHttpException:
                        return $this->error([], 'Endpoint não encontrado', Response::HTTP_NOT_FOUND);

                    case $e instanceof AuthenticationException:
                        return $this->error([], 'Token inválido ou expirado', Response::HTTP_UNAUTHORIZED);

                    case $e instanceof TooManyRequestsHttpException:
                        return $this->error([], 'Muitas tentativas. Tente novamente em alguns minutos', Response::HTTP_TOO_MANY_REQUESTS);

                    default:
                        $message = app()->environment('production')
                            ? 'Erro interno do servidor'
                            : $e->getMessage();

                        return $this->error([], $message, Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }
        });
    }

    /**
     * Return standardized JSON error response
     */
    private function error($errors = [], $message = '', $statusCode = Response::HTTP_BAD_REQUEST)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }
}
