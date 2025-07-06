<x-filament-widgets::widget>
    @if($isVisible)
        <x-filament::section>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold">No Domains Configured</h2>
                    <p>
                        Your application requires at least one domain to function correctly.
                        Click here to add the current domain.
                    </p>
                </div>
                <x-filament::button
                    wire:click="create"
                    color="primary">
                    Add Current Domain
                </x-filament::button>
            </div>
        </x-filament::section>
    @endif
</x-filament-widgets::widget>
