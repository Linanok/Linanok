<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Resources\DomainResource\RelationManagers\LinksRelationManager;
use App\Filament\Resources\LinkResource;
use App\Models\Link;
use Filament\Forms\Form;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MostVisitedLinks extends BaseWidget
{
    protected static ?string $heading = 'Most Visited Links';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()->can('view link');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Link::query()
                    ->orderBy('visit_count', 'desc')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('short_path')
                    ->label('Short URL')
                    ->searchable()
                    ->sortable()
                    ->url(function ($record, $livewire) {
                        if ($livewire instanceof LinksRelationManager) {
                            $domain = $livewire->ownerRecord;
                        } else {
                            $domain = null;
                        }

                        return get_short_url($record, $domain);
                    })
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->iconPosition('after'),

                TextColumn::make('original_url')
                    ->label('Original URL')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function ($record) {
                        return $record->original_url;
                    }),

                IconColumn::make('has_password')
                    ->label('Protected')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open'),

                TextColumn::make('visit_count')
                    ->label('Visits')
                    ->sortable()
                    ->alignRight()
                    ->badge()
                    ->color('success'),
            ])
            ->actions([
                Action::make('copy')
                    ->icon('heroicon-m-clipboard')
                    ->tooltip('Copy URL')
                    ->action(fn ($livewire, Link $record) => $livewire->dispatch('copy-to-clipboard', ['value' => LinkResource::getShortUrl($record, $livewire)])),

                ViewAction::make()
                    ->form(fn (Form $form): Form => LinkResource::form($form))
                    ->visible(fn ($record) => auth()->user()->can('view', $record) && ! auth()->user()->can('update', $record)),

                EditAction::make()
                    ->form(fn (Form $form): Form => LinkResource::form($form))
                    ->visible(fn ($record) => auth()->user()->can('update', $record)),
            ])
            ->paginated(false)
            ->striped();
    }
}
