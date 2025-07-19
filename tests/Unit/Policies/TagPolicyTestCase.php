<?php

namespace Tests\Unit\Policies;

use App\Models\Tag;
use App\Policies\TagPolicy;

class TagPolicyTestCase extends BasePolicyTestCase
{
    protected function setUp(): void
    {
        $this->modelName = 'tag';
        $this->policyClass = TagPolicy::class;
        $this->model = new Tag;

        parent::setUp();
    }
}
