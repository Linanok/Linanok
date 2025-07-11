<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LinkResourceFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an active domain with admin panel to satisfy validation
        Domain::factory()->create([
            'host' => 'default-test-domain.com',
            'is_active' => true,
            'is_admin_panel_active' => true,
        ]);

        // Create a super admin user
        $this->user = User::factory()->create([
            'is_super_admin' => true,
        ]);

        $this->actingAs($this->user);
    }

    #[Test]
    public function it_includes_is_active_field_in_form(): void
    {
        // Arrange & Act
        $link = Link::factory()->create(['is_active' => true]);

        // Assert
        $this->assertTrue($link->is_active);
        $this->assertIsBool($link->is_active);

        // Verify it's in the casts array
        $casts = $link->getCasts();
        $this->assertArrayHasKey('is_active', $casts);
        $this->assertEquals('boolean', $casts['is_active']);
    }

    #[Test]
    public function it_can_create_link_with_is_active_field(): void
    {
        // Arrange
        $domain = Domain::factory()->create([
            'is_active' => true,
        ]);

        $linkData = [
            'original_url' => 'https://example.com',
            'is_active' => false,
            'forward_query_parameters' => false,
            'send_ref_query_parameter' => false,
        ];

        // Act
        $link = Link::create($linkData);

        // Assert
        $this->assertDatabaseHas('links', [
            'original_url' => 'https://example.com',
            'is_active' => false,
        ]);

        $this->assertFalse($link->is_active);
        $this->assertFalse($link->isAvailable);
    }

    #[Test]
    public function it_can_update_link_is_active_status(): void
    {
        // Arrange
        $link = Link::factory()->create([
            'is_active' => true,
        ]);

        $this->assertTrue($link->is_active);

        // Act
        $link->update(['is_active' => false]);

        // Assert
        $this->assertFalse($link->fresh()->is_active);
        $this->assertFalse($link->fresh()->isAvailable);
    }
}
