<?php

namespace App\Livewire;

use App\Models\Link;
use App\Services\LinkVisitService;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Component;

class LinkPage extends Component implements HasForms
{
    use InteractsWithForms;

    public Link $link;

    public array $data = [];

    public function mount(?string $short_path)
    {
        $this->link = Link::available()
            ->where('short_path', $short_path)
            ->forCurrentDomain()
            ->firstOrFail();

        if (! $this->link->hasPassword) {
            return LinkVisitService::redirectToOriginalUrl($this->link);
        }
    }

    public function render()
    {
        if (! $this->link->hasPassword) {
            return;
        }

        return view('livewire.password-protected-link-page')
            ->layoutData([
                'title' => 'Password Protected Link',
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Password Protected')
                    ->schema([
                        TextInput::make('password')
                            ->label('Please enter the password to access this link')
                            ->placeholder('******')
                            ->password()
                            ->revealable()
                            ->required(),

                        ViewField::make('submit')
                            ->view('components.filament-button')
                            ->viewData([
                                'text' => 'Submit',
                                'click' => 'submit',
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function submit()
    {
        $password = $this->data['password'] ?? null;

        if (empty($password)) {
            Notification::make()
                ->title('Password is required')
                ->warning()
                ->send();

            return;
        }

        if ($password !== $this->link->password) {
            Notification::make()
                ->title('Password is wrong')
                ->danger()
                ->send();

            return;
        }

        return LinkVisitService::redirectToOriginalUrl($this->link);
    }
}
