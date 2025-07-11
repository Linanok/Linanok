<?php

namespace Filament\Admin\Widgets;

use App\Filament\Admin\Widgets\QuickLinkCreator;
use App\Models\Domain;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class QuickLinkCreatorTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Domain $domain;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_render_the_widget()
    {
        $this->actingAs($this->user);

        Livewire::test(QuickLinkCreator::class)
            ->assertSuccessful();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_contains_the_original_url_field()
    {
        $this->actingAs($this->user);

        Livewire::test(QuickLinkCreator::class)
            ->assertFormExists()
            ->assertFormFieldExists('original_url');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_create_a_link()
    {
        $this->actingAs($this->user);

        Livewire::test(QuickLinkCreator::class)
            ->fillForm([
                'original_url' => 'https://example.org',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('links', [
            'original_url' => 'https://example.org',
            'forward_query_parameters' => false,
            'send_ref_query_parameter' => false,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_validates_the_original_url()
    {
        $this->actingAs($this->user);

        Livewire::test(QuickLinkCreator::class)
            ->fillForm([
                'original_url' => 'not-a-valid-url',
            ])
            ->call('create')
            ->assertHasFormErrors(['original_url']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_requires_the_original_url()
    {
        $this->actingAs($this->user);

        Livewire::test(QuickLinkCreator::class)
            ->fillForm([
                'original_url' => '',
            ])
            ->call('create')
            ->assertHasFormErrors(['original_url' => 'required']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_redirect_to_advanced_options()
    {
        $this->actingAs($this->user);

        Livewire::test(QuickLinkCreator::class)
            ->fillForm([
                'original_url' => 'https://example.org',
            ])
            ->call('redirectToAdvancedOptions')
            ->assertRedirect(route('filament.admin.resources.links.create'));

        $this->assertEquals('https://example.org', session('original_url'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_is_only_visible_to_users_with_create_link_permission()
    {
        // User with permission
        $this->actingAs($this->user);
        $this->assertTrue(QuickLinkCreator::canView());

        // User without permission
        $userWithoutPermission = User::factory()->create();
        $this->actingAs($userWithoutPermission);
        $this->assertFalse(QuickLinkCreator::canView());
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Create a domain for testing
        $this->domain = Domain::factory()->create([
            'host' => 'example.com',
            'is_active' => true,
            'is_admin_panel_active' => true,
        ]);

        // Create a user with permissions
        $this->user = User::factory()->create();
        $role = Role::create(['name' => 'Admin']);
        Permission::create(['name' => 'create link']);
        $role->givePermissionTo('create link');
        $this->user->assignRole('Admin');
    }
}
