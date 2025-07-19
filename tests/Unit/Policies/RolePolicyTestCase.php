<?php

namespace Tests\Unit\Policies;

use App\Models\Role;
use App\Policies\RolePolicy;

class RolePolicyTestCase extends BasePolicyTestCase
{
    protected function setUp(): void
    {
        $this->modelName = 'role';
        $this->policyClass = RolePolicy::class;

        // Create the model after parent setup to ensure the database is properly set up
        parent::setUp();

        // Use an existing role instead of creating a new one
        $this->model = Role::first();
    }
}
