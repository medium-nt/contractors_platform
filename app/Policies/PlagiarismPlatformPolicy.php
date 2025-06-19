<?php

namespace App\Policies;

use App\Models\PlagiarismPlatform;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PlagiarismPlatformPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role->name == 'admin';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PlagiarismPlatform $plagiarismPlatform): bool
    {
        return $user->role->name == 'admin';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role->name == 'admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PlagiarismPlatform $plagiarismPlatform): bool
    {
        return $user->role->name == 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PlagiarismPlatform $plagiarismPlatform): bool
    {
        return $user->role->name == 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PlagiarismPlatform $plagiarismPlatform): bool
    {
        return $user->role->name == 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PlagiarismPlatform $plagiarismPlatform): bool
    {
        return $user->role->name == 'admin';
    }
}
