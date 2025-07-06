<?php

namespace App\Policies;

/**
 * Link Policy
 *
 * Handles authorization for Link model operations.
 * Extends the BasePolicy to provide standard CRUD permission checks
 * using the 'link' permission prefix.
 *
 * @see \App\Models\Link
 * @see \App\Policies\BasePolicy
 */
class LinkPolicy extends BasePolicy
{
    /** @var string The model name used for permission checking */
    protected string $modelName = 'link';
}
