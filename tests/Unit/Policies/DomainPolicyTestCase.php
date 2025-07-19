<?php

namespace Tests\Unit\Policies;

use App\Models\Domain;
use App\Policies\DomainPolicy;

class DomainPolicyTestCase extends BasePolicyTestCase
{
    protected function setUp(): void
    {
        $this->modelName = 'domain';
        $this->policyClass = DomainPolicy::class;
        $this->model = new Domain;

        parent::setUp();
    }
}
