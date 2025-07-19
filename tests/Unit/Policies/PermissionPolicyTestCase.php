<?php

namespace Tests\Unit\Policies;

use App\Models\Permission;
use App\Policies\PermissionPolicy;

class PermissionPolicyTestCase extends BasePolicyTestCase
{
    protected function setUp(): void
    {
        $this->modelName = 'permission';
        $this->policyClass = PermissionPolicy::class;

        // Create the model after parent setup to ensure the database is properly set up
        parent::setUp();

        // Use an existing permission instead of creating a new one
        $this->model = Permission::first();
    }
}
