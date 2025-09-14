<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Api\V1\VehicleImage\UploadImagesRequest;
use App\Services\VehicleImageService;
use App\Services\VehicleService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class VehicleImageController extends BaseController
{
    use AuthorizesRequests;

    public function __construct(
        private VehicleImageService $vehicleImageService,
        private VehicleService $vehicleService
    ) {}

    /**
     * Upload multiple images for a vehicle
     *
     * @param UploadImagesRequest $request
     * @param int $vehicleId
     * @return JsonResponse
     */
    public function store(UploadImagesRequest $request, int $vehicleId): JsonResponse
    {
        $vehicle = $this->vehicleService->getVehicleById($vehicleId);

        if (!$vehicle) {
            return $this->notFoundResponse('Veículo não encontrado');
        }

        $this->authorize('manageImages', $vehicle);

        try {
            $images = $this->vehicleImageService->uploadImages(
                $vehicle,
                $request->file('files')
            );

            return $this->createdResponse($images, 'Imagens enviadas com sucesso');
        } catch (\Exception $e) {
            return $this->errorResponse('Erro ao enviar imagens', 500);
        }
    }

    /**
     * Set an image as cover for the vehicle
     *
     * @param SetCoverImageRequest $request
     * @param int $vehicleId
     * @param int $imageId
     * @return JsonResponse
     */
    public function setCover(Request $request, int $vehicleId, int $imageId): JsonResponse
    {
        $vehicle = $this->vehicleService->getVehicleById($vehicleId);

        if (!$vehicle) {
            return $this->notFoundResponse('Veículo não encontrado');
        }

        $this->authorize('manageImages', $vehicle);

        $image = $this->vehicleImageService->setCoverImage($vehicle, $imageId);

        if (!$image) {
            return $this->notFoundResponse('Imagem não encontrada para este veículo');
        }

        return $this->successResponse($image, 'Imagem de capa definida com sucesso');
    }

    /**
     * Delete an image from the vehicle
     *
     * @param int $vehicleId
     * @param int $imageId
     * @return JsonResponse
     */
    public function destroy(int $vehicleId, int $imageId): JsonResponse
    {
        $vehicle = $this->vehicleService->getVehicleById($vehicleId);

        if (!$vehicle) {
            return $this->notFoundResponse('Veículo não encontrado');
        }

        $this->authorize('manageImages', $vehicle);

        $deleted = $this->vehicleImageService->deleteImage($vehicle, $imageId);

        if (!$deleted) {
            return $this->notFoundResponse('Imagem não encontrada para este veículo');
        }

        return $this->successResponse(null, 'Imagem excluída com sucesso');
    }
}
