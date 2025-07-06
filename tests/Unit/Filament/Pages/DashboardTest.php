<?php

namespace Filament\Pages;

use App\Filament\Admin\Widgets\AddCurrentDomain;
use App\Filament\Admin\Widgets\MostVisitedLinks;
use App\Filament\Admin\Widgets\QuickLinkCreator;
use App\Filament\Admin\Widgets\RecentActivity;
use App\Filament\Admin\Widgets\StatsOverview;
use App\Filament\Admin\Widgets\TopCountriesChart;
use App\Filament\Admin\Widgets\VisitsChart;
use App\Filament\Pages\Dashboard;
use App\Models\Domain;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Domain $domain;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a domain for testing
        $this->domain = Domain::factory()->create([
            'host' => 'example.com',
            'is_active' => true,
            'is_admin_panel_available' => true,
        ]);

        // Create a user
        $this->user = User::factory()->create();
        $role = Role::create(['name' => 'Admin']);
        $this->user->assignRole('Admin');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_correct_widgets()
    {
        $dashboard = new Dashboard;
        $widgets = $dashboard->getWidgets();

        $this->assertCount(7, $widgets);
        $this->assertContains(AddCurrentDomain::class, $widgets);
        $this->assertContains(QuickLinkCreator::class, $widgets);
        $this->assertContains(StatsOverview::class, $widgets);
        $this->assertContains(MostVisitedLinks::class, $widgets);
        $this->assertContains(RecentActivity::class, $widgets);
        $this->assertContains(TopCountriesChart::class, $widgets);
        $this->assertContains(VisitsChart::class, $widgets);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_has_correct_widget_order()
    {
        $dashboard = new Dashboard;
        $widgets = $dashboard->getWidgets();

        // Check that widgets are in the correct order
        $this->assertEquals(AddCurrentDomain::class, $widgets[0]);
        $this->assertEquals(QuickLinkCreator::class, $widgets[1]);
        $this->assertEquals(StatsOverview::class, $widgets[2]);
        $this->assertEquals(MostVisitedLinks::class, $widgets[3]);
        $this->assertEquals(RecentActivity::class, $widgets[4]);
        $this->assertEquals(TopCountriesChart::class, $widgets[5]);
        $this->assertEquals(VisitsChart::class, $widgets[6]);
    }
}
