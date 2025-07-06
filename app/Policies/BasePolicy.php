<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

/**
 * Base Policy Class
 *
 * Provides a standardized implementation of common authorization methods
 * for all model policies in the application. This base class implements
 * the standard CRUD permissions using the Spatie Permission package.
 *
 * Child policies should define the $modelName property to specify
 * which model permissions to check against.
 *
 * @see \Spatie\Permission\Traits\HasRoles
 * @see \App\Console\Commands\PopulatePermissions
 */
abstract class BasePolicy
{
    use HandlesAuthorization;

    /** @var string The model name used for permission checking (e.g., 'user', 'link') */
    protected string $modelName;

    /**
     * Determine whether the user can view any models.
     *
     * @param  User  $user  The user attempting the action
     * @return bool True if the user can view any models
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view '.$this->modelName);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user  The user attempting the action
     * @param  Model  $model  The model instance being accessed
     * @return bool True if the user can view the model
     */
    public function view(User $user, Model $model): bool
    {
        return $user->can('view '.$this->modelName);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user  The user attempting the action
     * @return bool True if the user can create models
     */
    public function create(User $user): bool
    {
        return $user->can('create '.$this->modelName);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user  The user attempting the action
     * @param  Model  $model  The model instance being updated
     * @return bool True if the user can update the model
     */
    public function update(User $user, Model $model): bool
    {
        return $user->can('update '.$this->modelName);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user  The user attempting the action
     * @param  Model  $model  The model instance being deleted
     * @return bool True if the user can delete the model
     */
    public function delete(User $user, Model $model): bool
    {
        return $user->can('delete '.$this->modelName);
    }
}
