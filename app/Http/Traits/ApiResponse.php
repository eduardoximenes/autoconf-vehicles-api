<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponse
{
    protected function successResponse($data = null, string $message = 'Success', int $code = Response::HTTP_OK): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    protected function errorResponse(string $message = 'Error', int $code = Response::HTTP_BAD_REQUEST, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!is_null($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    protected function createdResponse($data = null, string $message = 'Criado com sucesso'): JsonResponse
    {
        return $this->successResponse($data, $message, Response::HTTP_CREATED);
    }

    protected function notFoundResponse(string $message = 'Recurso não encontrado'): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_NOT_FOUND);
    }

    protected function unauthorizedResponse(string $message = 'Não autorizado'): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_UNAUTHORIZED);
    }

    protected function validationErrorResponse($errors, string $message = 'Dados inválidos'): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_UNPROCESSABLE_ENTITY, $errors);
    }

    protected function paginatedResponse($data, string $message = 'Success'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ]);
    }
}
