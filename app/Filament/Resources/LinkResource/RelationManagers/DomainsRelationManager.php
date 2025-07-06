<?php

namespace App\Filament\Resources\LinkResource\RelationManagers;

use App\Filament\Resources\DomainResource;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Table;

class DomainsRelationManager extends RelationManager
{
    protected static string $relationship = 'domains';

    public function form(Form $form): Form
    {
        return DomainResource::form($form);
    }

    public function table(Table $table): Table
    {
        return DomainResource::table($table)
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
