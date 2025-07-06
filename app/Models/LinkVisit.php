<?php

namespace App\Models;

use App\Observers\LinkVisitObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Link Visit Model
 *
 * Represents analytics data for individual link visits.
 * Each visit record captures information about when and how a link was accessed,
 * including browser, platform, country, and IP address information.
 *
 * @see \App\Models\Link
 * @see \App\Jobs\SaveLinkVisitJob
 * @see \App\Observers\LinkVisitObserver
 */
#[ObservedBy([LinkVisitObserver::class])]
class LinkVisit extends Model
{
    use HasFactory, HasTimestamps;

    /** @var string|null Disable updated_at timestamp as visits are immutable */
    public const UPDATED_AT = null;

    protected $guarded = [];

    /**
     * Get the link that was visited.
     *
     * @return BelongsTo<Link> The link relationship
     */
    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class);
    }
}
