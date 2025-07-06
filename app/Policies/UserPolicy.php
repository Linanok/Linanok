<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * User Policy
 *
 * Handles authorization for User model operations.
 * Extends the BasePolicy to provide standard CRUD permission checks
 * using the 'user' permission prefix, with additional logic to allow
 * users to update their own profiles.
 *
 * @see \App\Models\User
 * @see \App\Policies\BasePolicy
 */
class UserPolicy extends BasePolicy
{
    /** @var string The model name used for permission checking */
    protected string $modelName = 'user';

    /**
     * Determine whether the user can update the model.
     *
     * Users can update their own profile even without explicit permission,
     * or if they have the general 'update user' permission.
     *
     * @param  User  $user  The user attempting the action
     * @param  Model  $model  The user model instance being updated
     * @return bool True if the user can update the model
     */
    public function update(User $user, Model $model): bool
    {
        return $user->id === $model->id || parent::update($user, $model);
    }
}
