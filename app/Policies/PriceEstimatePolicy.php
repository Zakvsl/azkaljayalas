<?php

namespace App\Policies;

use App\Models\PriceEstimate;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PriceEstimatePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any estimates.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view estimates
    }

    /**
     * Determine whether the user can view the estimate.
     */
    public function view(User $user, PriceEstimate $estimate): bool
    {
        return $user->id === $estimate->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can create estimates.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create estimates
    }

    /**
     * Determine whether the user can update the estimate.
     */
    public function update(User $user, PriceEstimate $estimate): bool
    {
        // Only admins can update after status is confirmed
        if ($estimate->status === 'confirmed') {
            return $user->isAdmin();
        }

        return $user->id === $estimate->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the estimate.
     */
    public function delete(User $user, PriceEstimate $estimate): bool
    {
        // Can't delete confirmed estimates
        if ($estimate->status === 'confirmed') {
            return false;
        }

        return $user->id === $estimate->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can confirm or reject the estimate.
     */
    public function manageStatus(User $user, PriceEstimate $estimate): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can set actual price for the estimate.
     */
    public function setActualPrice(User $user, PriceEstimate $estimate): bool
    {
        return $user->isAdmin();
    }
}