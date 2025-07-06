<?php

namespace App\Observers;

use App\Models\Link;
use Illuminate\Support\Str;

/**
 * Link Observer
 *
 * Handles link model events to automatically generate unique short paths.
 * This observer ensures that every link has a unique short_path that can be
 * used in URLs, either based on a custom slug or randomly generated.
 *
 * @see \App\Models\Link
 */
class LinkObserver
{
    /**
     * Handle the Link "creating" event.
     *
     * Automatically generates a unique short_path for the link based on:
     * 1. The provided slug (if available) - ensures uniqueness by adding random suffix if needed
     * 2. A randomly generated string (if no slug provided)
     *
     * @param  Link  $link  The link being created
     */
    public function creating(Link $link): void
    {
        if (isset($link->slug)) {
            $link->short_path = $this->generateUniqueSlug($link, $link->slug);

            return;
        }

        // Generate a default unique slug if no slug is provided
        $link->short_path = $this->generateUniqueSlug($link);
    }

    /**
     * Generate a unique slug for the link.
     *
     * If a base slug is provided, it first tries to use it as-is. If that's taken,
     * it appends a random 6-character string. If no base slug is provided,
     * it generates a completely random 6-character string.
     *
     * @param  Link  $link  The link instance (for potential future use)
     * @param  string|null  $baseSlug  The desired slug base
     * @return string A unique slug that doesn't exist in the database
     */
    private function generateUniqueSlug(Link $link, ?string $baseSlug = null): string
    {
        if ($baseSlug) {
            // First try the original slug
            if (! Link::where('short_path', $baseSlug)->exists()) {
                return $baseSlug;
            }

            // If taken, try with random postfix until we find a unique one
            do {
                $slug = $baseSlug.Str::random(6);
                $exists = Link::where('short_path', $slug)->exists();
            } while ($exists);

            return $slug;
        }

        // Generate random slug if no base slug provided
        do {
            $slug = Str::random(6);
            $exists = Link::where('short_path', $slug)->exists();
        } while ($exists);

        return $slug;
    }
}
