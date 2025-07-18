<?php

namespace App\Policies;

/**
 * User Policy
 *
 * Handles authorization for User model operations.
 * Extends the BasePolicy to provide standard CRUD permission checks
 * using the 'user' permission prefix
 *
 * @see \App\Models\User
 * @see \App\Policies\BasePolicy
 */
class UserPolicy extends BasePolicy
{
    /** @var string The model name used for permission checking */
    protected string $modelName = 'user';
}
