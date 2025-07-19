<?php

namespace App\Filament\Resources\RoleResource\RelationManagers;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public function form(Form $form): Form
    {
        return UserResource::form($form);
    }

    public function table(Table $table): Table
    {
        return UserResource::table($table)
            ->headerActions([
                CreateAction::make(),
                AttachAction::make()
                    ->recordTitle(fn (User $record): string => $record->name)
                    ->recordSelectSearchColumns(['name', 'email']),
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
