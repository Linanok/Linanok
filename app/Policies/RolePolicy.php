<?php

namespace App\Policies;

/**
 * Role Policy
 *
 * Handles authorization for Role model operations.
 * Extends the BasePolicy to provide standard CRUD permission checks
 * using the 'role' permission prefix.
 *
 * @see \App\Models\Role
 * @see \App\Policies\BasePolicy
 */
class RolePolicy extends BasePolicy
{
    /** @var string The model name used for permission checking */
    protected string $modelName = 'role';
}
