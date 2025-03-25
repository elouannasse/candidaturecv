<?php

namespace App\Policies;

use App\Models\Offre;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OffrePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Offre $offre): bool
    {
        return ($user->role_id === 2 && $user->id === $offre->recruter_id) || $user->role_id === 3;  //admin and recruteur

    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role_id === 2; // recruteur role
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Offre $offre): bool
    {
        // return $user->role_id === 2; // recruteur
        return ($user->role_id === 2 && $user->id === $offre->recruter_id) || $user->role_id === 3;  //admin and recruteur
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Offre $offre): bool
    {
        return ($user->role_id === 2 && $user->id === $offre->recruter_id) || $user->role_id === 3; // recruteur et admin
    }


    public function apply(User $user, Offre $offre)
    {
        return $user->role_id === 1;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Offre $offre): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Offre $offre): bool
    {
        return false;
    }
}
