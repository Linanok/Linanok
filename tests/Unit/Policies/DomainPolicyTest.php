<?php

namespace Tests\Unit\Policies;

use App\Models\Domain;
use App\Policies\DomainPolicy;

class DomainPolicyTest extends BasePolicyTest
{
    protected function setUp(): void
    {
        $this->modelName = 'domain';
        $this->policyClass = DomainPolicy::class;
        $this->model = new Domain;

        parent::setUp();
    }
}
