<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\Api\V1\Vehicle\IndexRequest;
use App\Http\Requests\Api\V1\Vehicle\StoreRequest;
use App\Http\Requests\Api\V1\Vehicle\UpdateRequest;
use App\Http\Controllers\Api\V1\BaseController;
use Illuminate\Http\JsonResponse;
use App\Services\VehicleService;
use App\Models\Vehicle;

class VehicleController extends BaseController
{
    use AuthorizesRequests;

    public function __construct(
        private VehicleService $vehicleService
    ) {}

    /**
     * @OA\Get(
     *     path="/vehicles",
     *     tags={"Veículos"},
     *     summary="Listar veículos com paginação e filtros",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número da página",
     *         @OA\Schema(type="integer", minimum=1, example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Itens por página (máx. 100)",
     *         @OA\Schema(type="integer", minimum=1, maximum=100, example=10)
     *     ),
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Busca global (license_plate, brand, model)",
     *         @OA\Schema(type="string", example="Toyota")
     *     ),
     *     @OA\Parameter(
     *         name="brand",
     *         in="query",
     *         description="Filtrar por marca",
     *         @OA\Schema(type="string", example="Toyota")
     *     ),
     *     @OA\Parameter(
     *         name="model",
     *         in="query",
     *         description="Filtrar por modelo",
     *         @OA\Schema(type="string", example="Corolla")
     *     ),
     *     @OA\Parameter(
     *         name="license_plate",
     *         in="query",
     *         description="Filtrar por placa",
     *         @OA\Schema(type="string", example="ABC1D23")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Ordenação (ex: km,-sale_price)",
     *         @OA\Schema(type="string", example="km,-sale_price")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de veículos",
     *         @OA\JsonContent(ref="#/components/schemas/PaginatedVehicleResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autenticado",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")
     *     )
     * )
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Vehicle::class);

        $vehicles = $this->vehicleService->getPaginatedVehicles($request);

        return $this->paginatedResponse($vehicles, 'Veículos listados com sucesso');
    }

    /**
     * @OA\Post(
     *     path="/vehicles",
     *     tags={"Veículos"},
     *     summary="Criar novo veículo",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/VehicleStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Veículo criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Veículo criado com sucesso"),
     *             @OA\Property(property="data", ref="#/components/schemas/Vehicle")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autenticado",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $this->authorize('create', Vehicle::class);

        $vehicle = $this->vehicleService->createVehicle(
            $request->validated(),
            $request->user()->id
        );

        return $this->createdResponse($vehicle, 'Veículo criado com sucesso');
    }

    /**
     * @OA\Get(
     *     path="/vehicles/{id}",
     *     tags={"Veículos"},
     *     summary="Obter detalhes do veículo",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do veículo",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes do veículo",
     *         @OA\JsonContent(ref="#/components/schemas/VehicleSuccessResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autenticado",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Veículo não encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/NotFoundError")
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $vehicle = $this->vehicleService->getVehicleById($id);

        if (!$vehicle) {
            return $this->notFoundResponse('Veículo não encontrado');
        }

        $this->authorize('view', $vehicle);

        return $this->successResponse($vehicle, 'Veículo encontrado com sucesso');
    }

    /**
     * @OA\Put(
     *     path="/vehicles/{id}",
     *     tags={"Veículos"},
     *     summary="Atualizar veículo",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do veículo",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/VehicleStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Veículo atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Veículo atualizado com sucesso"),
     *             @OA\Property(property="data", ref="#/components/schemas/Vehicle")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autenticado",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Ação não autorizada",
     *         @OA\JsonContent(ref="#/components/schemas/ForbiddenError")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Veículo não encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/NotFoundError")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     */
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        $vehicle = $this->vehicleService->getVehicleById($id);

        if (!$vehicle) {
            return $this->notFoundResponse('Veículo não encontrado');
        }

        $this->authorize('update', $vehicle);

        $updatedVehicle = $this->vehicleService->updateVehicle(
            $vehicle,
            $request->validated(),
            $request->user()->id
        );

        return $this->successResponse($updatedVehicle, 'Veículo atualizado com sucesso');
    }

    /**
     * @OA\Delete(
     *     path="/vehicles/{id}",
     *     tags={"Veículos"},
     *     summary="Excluir veículo",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do veículo",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Veículo excluído com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Veículo excluído com sucesso"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autenticado",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Ação não autorizada",
     *         @OA\JsonContent(ref="#/components/schemas/ForbiddenError")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Veículo não encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/NotFoundError")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erro ao excluir o veículo")
     *         )
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $vehicle = $this->vehicleService->getVehicleById($id);

        if (!$vehicle) {
            return $this->notFoundResponse('Veículo não encontrado');
        }

        $this->authorize('delete', $vehicle);

        $deleted = $this->vehicleService->deleteVehicle($vehicle);

        if (!$deleted) {
            return $this->errorResponse('Erro ao excluir o veículo', 500);
        }

        return $this->successResponse(null, 'Veículo excluído com sucesso');
    }

}
