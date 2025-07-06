<?php

namespace App\Models;

use App\History\MyLogsActivity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Models\Role as BaseRole;

/**
 * Role Model
 *
 * Extends the Spatie Permission package's base Role model.
 * Roles are collections of permissions that can be assigned to users,
 * providing a convenient way to manage user authorization.
 *
 * @see \Spatie\Permission\Models\Role
 * @see \App\Models\Permission
 * @see \App\Models\User
 */
class Role extends BaseRole
{
    use LogsActivity, MyLogsActivity;

    public function __toString()
    {
        return $this->name;
    }
}
