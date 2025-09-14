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
     * @OA\Post(
     *     path="/vehicles/{vehicleId}/images",
     *     tags={"Imagens"},
     *     summary="Upload múltiplo de imagens",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="vehicleId",
     *         in="path",
     *         required=true,
     *         description="ID do veículo",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="files[]",
     *                     type="array",
     *                     @OA\Items(type="string", format="binary"),
     *                     description="Array de imagens (máx. 2MB cada)"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Imagens enviadas com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/VehicleImage")
     *             ),
     *             @OA\Property(property="message", type="string", example="Imagens enviadas com sucesso")
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
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erro ao enviar imagens")
     *         )
     *     )
     * )
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
     * @OA\Patch(
     *     path="/vehicles/{vehicleId}/images/{imageId}/cover",
     *     tags={"Imagens"},
     *     summary="Definir imagem como capa",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="vehicleId",
     *         in="path",
     *         required=true,
     *         description="ID do veículo",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="imageId",
     *         in="path",
     *         required=true,
     *         description="ID da imagem",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Capa definida com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/VehicleImage"),
     *             @OA\Property(property="message", type="string", example="Imagem de capa definida com sucesso")
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
     *         description="Veículo ou imagem não encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/NotFoundError")
     *     )
     * )
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
     * @OA\Delete(
     *     path="/vehicles/{vehicleId}/images/{imageId}",
     *     tags={"Imagens"},
     *     summary="Excluir imagem do veículo",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="vehicleId",
     *         in="path",
     *         required=true,
     *         description="ID do veículo",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="imageId",
     *         in="path",
     *         required=true,
     *         description="ID da imagem",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Imagem excluída com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Imagem excluída com sucesso")
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
     *         description="Veículo ou imagem não encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/NotFoundError")
     *     )
     * )
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
