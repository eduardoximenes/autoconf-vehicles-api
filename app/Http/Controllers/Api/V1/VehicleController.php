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

    public function index(IndexRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Vehicle::class);

        $vehicles = $this->vehicleService->getPaginatedVehicles($request);

        return $this->paginatedResponse($vehicles, 'Veículos listados com sucesso');
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $this->authorize('create', Vehicle::class);

        $vehicle = $this->vehicleService->createVehicle(
            $request->validated(),
            $request->user()->id
        );

        return $this->createdResponse($vehicle, 'Veículo criado com sucesso');
    }

    public function show(int $id): JsonResponse
    {
        $vehicle = $this->vehicleService->getVehicleById($id);

        if (!$vehicle) {
            return $this->notFoundResponse('Veículo não encontrado');
        }

        $this->authorize('view', $vehicle);

        return $this->successResponse($vehicle, 'Veículo encontrado com sucesso');
    }

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
