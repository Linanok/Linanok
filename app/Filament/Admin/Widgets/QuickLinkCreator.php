<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Domain;
use App\Models\Link;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\HtmlString;

class QuickLinkCreator extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.admin.widgets.quick-link-creator';

    public ?array $data = [];

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()->can('create link');
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('original_url')
                                    ->label('URL to Shorten')
                                    ->required()
                                    ->url()
                                    ->placeholder('https://example.com')
                                    ->helperText(new HtmlString('Need advanced options? <a href="#" wire:click.prevent="redirectToAdvancedOptions" class="text-primary-600 hover:text-primary-500">Create with full options</a>'))
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $domainIds = Domain::pluck('id')->toArray();

        if (! $domainIds) {
            Notification::make()
                ->danger()
                ->title('No Domains Found')
                ->body('Please add at least one domain before creating a shortened link.')
                ->actions([
                    Action::make('add_domain')
                        ->label('Add Domain')
                        ->button()
                        ->color('primary')
                        ->url(route('filament.admin.resources.domains.create')),

                    Action::make('add_current_domain')
                        ->label('Add Current Domain')
                        ->button()
                        ->color('gray')
                        ->url(route('filament.admin.resources.domains.create-current')),
                ])
                ->persistent()
                ->send();

            return;
        }

        $data = $this->form->getState();

        $link = Link::create([
            'original_url' => $data['original_url'],
            'forward_query_parameters' => false,
            'send_ref_query_parameter' => false,
            'is_active' => true,
        ]);
        $link->domains()->attach($domainIds);

        $this->form->fill();

        $shortenedUrl = route('link.redirect', ['short_path' => $link->short_path]);

        Notification::make()
            ->success()
            ->title('Link created successfully!')
            ->body(new HtmlString("Your shortened URL: <br/> <div class='flex items-center mt-2'><code class='bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded text-sm font-mono break-all'>$shortenedUrl</code></div>"))
            ->actions([
                Action::make('view')
                    ->button()
                    ->url(route('filament.admin.resources.links.edit', ['record' => $link]))
                    ->label('View Details'),

                Action::make('open')
                    ->color('gray')
                    ->url($shortenedUrl)
                    ->label('Open Link')
                    ->openUrlInNewTab(),
            ])
            ->persistent()
            ->send();

        // Stay on the same page to allow creating multiple links quickly
        $this->form->fill();
    }

    public function redirectToAdvancedOptions(): void
    {
        $data = $this->form->getState();

        if (! empty($data['original_url'])) {
            session()->flash('original_url', $data['original_url']);
        }
        redirect()->route('filament.admin.resources.links.create');
    }
}
