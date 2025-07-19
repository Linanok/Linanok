<?php

namespace App\Filament\Resources\TagResource\RelationManagers;

use App\Filament\Resources\LinkResource;
use App\Models\Link;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;

class LinksRelationManager extends RelationManager
{
    protected static string $relationship = 'links';

    public function form(Form $form): Form
    {
        return LinkResource::form($form);
    }

    public function table(Table $table): Table
    {
        return LinkResource::table($table)
            ->headerActions([
                CreateAction::make(),
                AttachAction::make()
                    ->recordTitle(fn (Link $record): string => "$record->short_path | $record->original_url")
                    ->recordSelectSearchColumns([
                        'short_path', 'original_url', 'slug', 'description',
                    ]),
            ])
            ->actions([
                // ...
                EditAction::make(),
                DetachAction::make(),
            ])
            ->bulkActions([
                // ...
                DetachBulkAction::make(),
            ]);
    }
}
