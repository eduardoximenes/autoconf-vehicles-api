<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;

class VehiclePolicy
{
    /**
     * Determine whether the user can view any vehicles.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the vehicle.
     */
    public function view(User $user, Vehicle $vehicle): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create vehicles.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the vehicle.
     * Apenas o dono do veículo (ou admin) pode editar.
     */
    public function update(User $user, Vehicle $vehicle): bool
    {
        return $user->is_admin || $user->id === $vehicle->user_id;
    }

    /**
     * Determine whether the user can delete the vehicle.
     * Apenas o dono do veículo (ou admin) pode excluir.
     */
    public function delete(User $user, Vehicle $vehicle): bool
    {
        return $user->is_admin || $user->id === $vehicle->user_id;
    }

    /**
     * Determine whether the user can manage images of the vehicle.
     * Apenas o dono do veículo (ou admin) pode gerenciar imagens.
     */
    public function manageImages(User $user, Vehicle $vehicle): bool
    {
        return $user->is_admin || $user->id === $vehicle->user_id;
    }
}
