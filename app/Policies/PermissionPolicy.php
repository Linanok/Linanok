<?php

namespace App\Policies;

/**
 * Permission Policy
 *
 * Handles authorization for Permission model operations.
 * Extends the BasePolicy to provide standard CRUD permission checks
 * using the 'permission' permission prefix.
 *
 * @see \App\Models\Permission
 * @see \App\Policies\BasePolicy
 */
class PermissionPolicy extends BasePolicy
{
    /** @var string The model name used for permission checking */
    protected string $modelName = 'permission';
}
