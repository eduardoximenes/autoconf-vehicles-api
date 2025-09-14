<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\VehicleImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VehicleImageService
{
    /**
     * Upload multiple images for a vehicle
     *
     * @param Vehicle $vehicle
     * @param array $files
     * @return array
     */
    public function uploadImages(Vehicle $vehicle, array $files): array
    {
        $uploadedImages = [];

        try {
            DB::transaction(function () use ($vehicle, $files, &$uploadedImages) {
                foreach ($files as $file) {
                    /** @var UploadedFile $file */
                    $path = $this->storeImageFile($file, $vehicle->id);

                    $image = VehicleImage::create([
                        'vehicle_id' => $vehicle->id,
                        'path' => $path,
                        'is_cover' => false,
                    ]);

                    $uploadedImages[] = $image;
                }

                // Se o veículo não tem nenhuma imagem de capa, define a primeira como capa
                if (!$vehicle->coverImage && !empty($uploadedImages)) {
                    $this->setCoverImage($vehicle, $uploadedImages[0]->id);
                    $uploadedImages[0]->is_cover = true;
                }
            });

            return $uploadedImages;
        } catch (\Exception $e) {
            Log::error('Error uploading vehicle images: ' . $e->getMessage());

            foreach ($uploadedImages as $image) {
                $this->deleteImageFile($image->path);
            }

            throw $e;
        }
    }

    /**
     * Set an image as cover for a vehicle
     *
     * @param Vehicle $vehicle
     * @param int $imageId
     * @return VehicleImage|null
     */
    public function setCoverImage(Vehicle $vehicle, int $imageId): ?VehicleImage
    {
        try {
            return DB::transaction(function () use ($vehicle, $imageId) {
                $image = VehicleImage::where('vehicle_id', $vehicle->id)
                    ->where('id', $imageId)
                    ->first();

                if (!$image) {
                    return null;
                }

                VehicleImage::where('vehicle_id', $vehicle->id)
                    ->where('is_cover', true)
                    ->where('id', '!=', $imageId)
                    ->update(['is_cover' => false]);

                $image->update(['is_cover' => true]);

                return $image->fresh();
            });
        } catch (\Exception $e) {
            Log::error('Error setting cover image: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete an image
     *
     * @param Vehicle $vehicle
     * @param int $imageId
     * @return bool
     */
    public function deleteImage(Vehicle $vehicle, int $imageId): bool
    {
        try {
            return DB::transaction(function () use ($vehicle, $imageId) {
                $image = VehicleImage::where('vehicle_id', $vehicle->id)
                    ->where('id', $imageId)
                    ->first();

                if (!$image) {
                    return false;
                }

                $wasCover = $image->is_cover;

                $this->deleteImageFile($image->path);
                $image->delete();

                if ($wasCover) {
                    $newCover = VehicleImage::where('vehicle_id', $vehicle->id)->first();
                    if ($newCover) {
                        $newCover->update(['is_cover' => true]);
                    }
                }

                return true;
            });
        } catch (\Exception $e) {
            Log::error('Error deleting vehicle image: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Store image file in storage
     *
     * @param UploadedFile $file
     * @param int $vehicleId
     * @return string
     */
    private function storeImageFile(UploadedFile $file, int $vehicleId): string
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $directory = 'vehicles/' . $vehicleId;

        $path = $file->storeAs($directory, $filename, 'public');

        return $path;
    }

    /**
     * Delete image file from storage
     *
     * @param string $path
     * @return void
     */
    private function deleteImageFile(string $path): void
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
