<?php

namespace App\Policies;

/**
 * Domain Policy
 *
 * Handles authorization for Domain model operations.
 * Extends the BasePolicy to provide standard CRUD permission checks
 * using the 'domain' permission prefix.
 *
 * @see \App\Models\Domain
 * @see \App\Policies\BasePolicy
 */
class DomainPolicy extends BasePolicy
{
    /** @var string The model name used for permission checking */
    protected string $modelName = 'domain';
}
