<?php

namespace App\Filament\Resources\DomainResource\Pages;

use App\Filament\Resources\DomainResource;
use App\Models\Domain;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\QueryException;

class EditDomain extends EditRecord
{
    protected static string $resource = DomainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->action(function (Domain $record) {
                    try {
                        $record->delete();

                        Notification::make()
                            ->success()
                            ->title('Domain deleted successfully')
                            ->send();

                        return redirect()->route('filament.admin.resources.domains.index');
                    } catch (QueryException $e) {
                        if (str_contains($e->getMessage(), 'FOREIGN KEY constraint failed') ||
                            str_contains($e->getMessage(), 'foreign key constraint')) {
                            Notification::make()
                                ->danger()
                                ->title('Cannot delete domain')
                                ->body('This domain cannot be deleted because it has links attached to it. Please remove all links from this domain first.')
                                ->send();
                        } else {
                            throw $e;
                        }
                    }
                })
                ->requiresConfirmation(),
        ];
    }

    protected function beforeSave(): void
    {
        if (! $this->data['is_admin_panel_available']) {
            $otherAdminPanelExists = Domain::adminPanelAvailable()
                ->where('id', '!=', $this->record->id)
                ->exists();

            if (! $otherAdminPanelExists) {
                if (! $this->data['is_admin_panel_available']) {
                    $this->addError('data.is_admin_panel_available', 'At least one active domain must have admin panel available.');
                }

                Notification::make()
                    ->danger()
                    ->title('Cannot disable admin panel')
                    ->body('At least one active domain must have admin panel available.')
                    ->send();

                $this->halt();
            }
        }
    }
}
