<?php

namespace App\Observers;

use App\Models\Link;
use App\Models\LinkVisit;

/**
 * Link Visit Observer
 *
 * Handles link visit model events to maintain visit count statistics.
 * This observer automatically increments the visit_count on the associated
 * link whenever a new visit record is created.
 *
 * @see \App\Models\LinkVisit
 * @see \App\Models\Link
 */
class LinkVisitObserver
{
    /**
     * Handle the LinkVisit "created" event.
     *
     * Automatically increments the visit_count field on the associated link
     * to maintain accurate visit statistics without requiring additional
     * database queries in the main application flow.
     *
     * @param  LinkVisit  $linkVisit  The visit record that was created
     */
    public function created(LinkVisit $linkVisit): void
    {
        Link::where('id', $linkVisit->link_id)->increment('visit_count');
    }
}
