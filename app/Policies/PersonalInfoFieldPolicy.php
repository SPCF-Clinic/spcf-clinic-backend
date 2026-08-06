<?php

namespace App\Policies;

use App\Models\PersonalInfoField;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PersonalInfoFieldPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PersonalInfoField $personalInfoField): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['ADMIN', 'SUPER_ADMIN']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PersonalInfoField $personalInfoField): bool
    {
        return $user->hasRole(['ADMIN', 'SUPER_ADMIN']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PersonalInfoField $personalInfoField): bool
    {
        return $user->hasRole(['ADMIN', 'SUPER_ADMIN']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PersonalInfoField $personalInfoField): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PersonalInfoField $personalInfoField): bool
    {
        return $user->hasRole(['SUPER_ADMIN']);
    }

    public function switchFormOrder(User $user): bool
    {
        return $user->hasRole(['ADMIN', 'SUPER_ADMIN']);
    }
}
