<?php

namespace Filament\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LinkPageLayoutTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_the_layout_with_slot_content()
    {
        $view = $this->blade(
            '<x-link-page-layout>Test Content</x-link-page-layout>'
        );

        $view->assertSee('Test Content', false);
        $view->assertSee('<title>Link Page</title>', false);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_includes_required_stylesheets_and_scripts()
    {
        $view = $this->blade(
            '<x-link-page-layout>Test Content</x-link-page-layout>'
        );

        // Check for stylesheets
        $view->assertSee('href="https://fonts.bunny.net"', false);
        $view->assertSee('css/filament/filament/app.css', false);

        // Check for scripts
        $view->assertSee('js/filament/filament/echo.js', false);
        $view->assertSee('js/filament/filament/app.js', false);
        $view->assertSee('app.js', false);
    }
}
