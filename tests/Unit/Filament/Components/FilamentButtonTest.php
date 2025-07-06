<?php

namespace Tests\Unit\Filament\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FilamentButtonTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_with_default_text(): void
    {
        // Act
        $view = $this->blade(
            '<x-filament-button :click="$click" />',
            ['click' => 'doSomething']
        );

        // Assert
        $view->assertSee('Button', false);
        $view->assertSee('wire:click="doSomething"', false);
    }

    #[Test]
    public function it_renders_with_custom_text(): void
    {
        // Act
        $view = $this->blade(
            '<x-filament-button :click="$click" :text="$text" />',
            ['click' => 'doSomething', 'text' => 'Custom Button Text']
        );

        // Assert
        $view->assertSee('Custom Button Text', false);
        $view->assertSee('wire:click="doSomething"', false);
    }
}
