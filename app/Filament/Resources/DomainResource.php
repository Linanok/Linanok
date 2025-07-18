<?php

namespace App\Filament\Resources;

use App\Enums\Protocol;
use App\Filament\Resources\DomainResource\Pages;
use App\Filament\Resources\DomainResource\RelationManagers\LinksRelationManager;
use App\History\HistoryAction;
use App\Models\Domain;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\ValidationException;

/**
 * Filament Resource for managing domains.
 *
 * This resource provides a complete CRUD interface for domains within the admin panel.
 * Domains are used to host shortened links and can optionally host the admin panel itself.
 * The resource includes validation to ensure at least one domain remains available for
 * admin panel access.
 *
 * @see \App\Models\Domain
 * @see \App\Enums\Protocol
 */
class DomainResource extends Resource
{
    protected static ?string $model = Domain::class;

    protected static ?string $slug = 'domains';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Link Management';

    protected static ?string $recordTitleAttribute = 'host';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('protocol')
                    ->options(Protocol::class)
                    ->default(Protocol::HTTPS)
                    ->required(),

                TextInput::make('host')
                    ->required()
                    ->placeholder('example.com:8080, localhost:8080, or 192.168.1.1:8080')
                    ->helperText('Domain name, localhost, or IP address with optional port number')
                    ->regex('/^localhost(:\d+)?$|^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)+([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])(:\d+)?$|^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})(:\d+)?$/')
                    ->validationAttribute('host')
                    ->maxLength(255),

                Toggle::make('is_active')
                    ->default(true),

                Toggle::make('is_admin_panel_active')
                    ->default(false),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn (?Domain $record): string => $record?->created_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('protocol'),

                TextColumn::make('host')
                    ->searchable(),

                IconColumn::make('is_active')
                    ->boolean(),

                IconColumn::make('is_admin_panel_active')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),

                EditAction::make(),

                DeleteAction::make(),

                HistoryAction::make(static::class),
            ])->bulkActions([
                DeleteBulkAction::make()->visible(fn () => request()->user()->can('delete domain')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDomains::route('/'),
            'create' => Pages\CreateDomain::route('/create'),
            'create-current' => Pages\CreateCurrentDomain::route('/create-current'),
            'edit' => Pages\EditDomain::route('/{record}/edit'),
            'history' => Pages\DomainHistory::route('/{record}/history'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }

    public static function getRelations(): array
    {
        return [
            LinksRelationManager::class,
        ];
    }

    protected function onValidationError(ValidationException $exception): void
    {
        Notification::make()
            ->title($exception->getMessage())
            ->danger()
            ->send();
    }
}
