<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as BasePermission;

/**
 * Permission Model
 *
 * Extends the Spatie Permission package's base Permission model.
 * Permissions are used to control access to various parts of the application
 * and can be assigned directly to users or through roles.
 *
 * @see \Spatie\Permission\Models\Permission
 * @see \App\Console\Commands\PopulatePermissions
 * @see \App\Policies\BasePolicy
 */
class Permission extends BasePermission
{
    public function __toString()
    {
        return $this->name;
    }
}
