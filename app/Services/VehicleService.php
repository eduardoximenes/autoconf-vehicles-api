<?php

namespace App\Services;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VehicleService
{
    /**
     * Apply filters to the query
     *
     * @param Builder $query
     * @param Request $request
     * @return void
     */
    private function applyFilters(Builder $query, Request $request): void
    {
        if ($request->filled('brand')) {
            $query->where('brand', 'like', '%' . $request->get('brand') . '%');
        }

        if ($request->filled('model')) {
            $query->where('model', 'like', '%' . $request->get('model') . '%');
        }

        if ($request->filled('license_plate')) {
            $query->where('license_plate', 'like', '%' . $request->get('license_plate') . '%');
        }
    }

    /**
     * Apply search to the query
     *
     * @param Builder $query
     * @param Request $request
     * @return void
     */
    private function applySearch(Builder $query, Request $request): void
    {
        if ($request->filled('q')) {
            $search = $request->get('q');

            $query->where(function (Builder $q) use ($search) {
                $q->where('license_plate', 'like', '%' . $search . '%')
                  ->orWhere('brand', 'like', '%' . $search . '%')
                  ->orWhere('model', 'like', '%' . $search . '%');
            });
        }
    }

    /**
     * Apply sorting to the query
     *
     * @param Builder $query
     * @param Request $request
     * @return void
     */
    private function applySorting(Builder $query, Request $request): void
    {
        $allowedSorts = [
            'sale_price',
            'km',
            'created_at'
        ];

        $sort = $request->get('sort');
        $sorts = [];

        if ($sort) {
            $sortFields = explode(',', $sort);

            foreach ($sortFields as $field) {
                $direction = 'asc';

                if (str_starts_with($field, '-')) {
                    $direction = 'desc';
                    $field = substr($field, 1);
                }

                if (in_array($field, $allowedSorts)) {
                    $sorts[] = [
                        'field' => $field,
                        'direction' => $direction
                    ];
                }
            }
        }

        if (empty($sorts)) {
            $sorts = [['field' => 'created_at', 'direction' => 'desc']];
        }

        foreach ($sorts as $sortItem) {
            $query->orderBy($sortItem['field'], $sortItem['direction']);
        }
    }

    /**
     * Delete image file from storage
     *
     * @param string $path
     * @return void
     */
    private function deleteImageFile(string $path): void
    {
        $fullPath = storage_path('app/public/' . $path);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    /**
     * Get paginated vehicles with filters and search
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getPaginatedVehicles(Request $request): LengthAwarePaginator
    {

        $query = Vehicle::query()->with(['user', 'coverImage']);

        $this->applyFilters($query, $request);
        $this->applySearch($query, $request);
        $this->applySorting($query, $request);

        $perPage = min((int) $request->get('per_page', 10), 100);
        $page = (int) $request->get('page', 1);

        return $query->paginate($perPage, ['*'], 'page', max($page, 1));
    }

    /**
     * Create a new vehicle
     *
     * @param array $data
     * @param int $userId
     * @return Vehicle
     */
    public function createVehicle(array $data, int $userId): Vehicle
    {
        return Vehicle::create([
            'license_plate' => strtoupper($data['license_plate']),
            'chassis' => strtoupper($data['chassis']),
            'brand' => $data['brand'],
            'model' => $data['model'],
            'version' => $data['version'],
            'sale_price' => $data['sale_price'],
            'color' => $data['color'],
            'km' => $data['km'],
            'transmission' => $data['transmission'],
            'fuel_type' => $data['fuel_type'],
            'user_id' => $userId,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);
    }

    /**
     * Get vehicle by ID with all relationships and audit info
     *
     * @param int $id
     * @return Vehicle|null
     */
    public function getVehicleById(int $id): ?Vehicle
    {
        return Vehicle::with([
            'user:id,name,email',
            'images',
            'createdBy:id,name,email',
            'updatedBy:id,name,email'
        ])->find($id);
    }

    /**
     * Update an existing vehicle
     *
     * @param Vehicle $vehicle
     * @param array $data
     * @param int $userId
     * @return Vehicle
     */
    public function updateVehicle(Vehicle $vehicle, array $data, int $userId): Vehicle
    {
        $updateData = [];

        if (isset($data['license_plate'])) {
            $updateData['license_plate'] = strtoupper($data['license_plate']);
        }

        if (isset($data['chassis'])) {
            $updateData['chassis'] = strtoupper($data['chassis']);
        }

        $fields = ['brand', 'model', 'version', 'sale_price', 'color', 'km', 'transmission', 'fuel_type'];
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }

        $updateData['updated_by'] = $userId;

        $vehicle->update($updateData);

        return $vehicle->fresh([
            'user:id,name,email',
            'images',
            'createdBy:id,name,email',
            'updatedBy:id,name,email'
        ]);
    }

    /**
     * Delete a vehicle and all its associated images
     *
     * @param Vehicle $vehicle
     * @return bool
     */
    public function deleteVehicle(Vehicle $vehicle): bool
    {
        try {
            DB::transaction(function () use ($vehicle) {
                foreach ($vehicle->images as $image) {
                    $this->deleteImageFile($image->path);
                    $image->delete();
                }

                $vehicle->delete();
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Error deleting vehicle: ' . $e->getMessage());
            return false;
        }
    }
}
