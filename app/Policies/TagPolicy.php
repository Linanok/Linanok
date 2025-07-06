<?php

namespace App\Policies;

/**
 * Tag Policy
 *
 * Handles authorization for Tag model operations.
 * Extends the BasePolicy to provide standard CRUD permission checks
 * using the 'tag' permission prefix.
 *
 * @see \App\Models\Tag
 * @see \App\Policies\BasePolicy
 */
class TagPolicy extends BasePolicy
{
    /** @var string The model name used for permission checking */
    protected string $modelName = 'tag';
}
