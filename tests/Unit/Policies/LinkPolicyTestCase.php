<?php

namespace Tests\Unit\Policies;

use App\Models\Link;
use App\Policies\LinkPolicy;

class LinkPolicyTestCase extends BasePolicyTestCase
{
    protected function setUp(): void
    {
        $this->modelName = 'link';
        $this->policyClass = LinkPolicy::class;
        $this->model = new Link;

        parent::setUp();
    }
}
