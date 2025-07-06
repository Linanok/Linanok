<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;

/**
 * Link Tag Pivot Model
 *
 * Represents the many-to-many relationship between links and tags.
 * This pivot table allows links to be tagged with multiple tags
 * and tags to be applied to multiple links for categorization.
 *
 * @see \App\Models\Link
 * @see \App\Models\Tag
 */
class LinkTag extends Model
{
    use HasTimestamps;

    /** @var string|null Disable updated_at timestamp as tag assignments are rarely updated */
    public const UPDATED_AT = null;
}
