<?php

namespace App\Observers;

use App\Models\Domain;
use Illuminate\Validation\ValidationException;

/**
 * Domain Observer
 *
 * Handles domain model events to enforce business rules and maintain system integrity.
 * This observer ensures that at least one active domain always has admin panel access
 * available, preventing administrators from being locked out of the system.
 *
 * @see \App\Models\Domain
 */
class DomainObserver
{
    /**
     * Handle the Domain "saving" event.
     *
     * Validates that at least one active domain will have admin panel access
     * after the save operation. If this resulted in no admin panel access,
     * the save is prevented with a validation exception.
     *
     * @param  Domain  $domain  The domain being saved
     *
     * @throws ValidationException When the save would remove all admin panel access
     */
    public function saving(Domain $domain): void
    {
        // Check if this save would remove all admin panel access
        if (! ($domain->is_admin_panel_available) && ! Domain::adminPanelAvailable()
            ->where('id', '!=', $domain->id)
            ->exists()) {
            throw ValidationException::withMessages([
                'is_admin_panel_available' => 'At least one active domain must have admin panel available.',
            ]);
        }
    }
}
