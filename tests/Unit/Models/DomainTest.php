<?php

namespace Tests\Unit\Models;

use App\Enums\Protocol;
use App\Models\Domain;
use App\Models\Link;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DomainTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_domain(): void
    {
        // Arrange & Act
        $domain = Domain::factory()->create([
            'host' => 'example.com:8080',
            'protocol' => Protocol::HTTPS,
            'is_active' => true,
            'is_admin_panel_active' => false,
        ]);

        // Assert
        $this->assertDatabaseHas('domains', [
            'host' => 'example.com:8080',
            'protocol' => Protocol::HTTPS->value,
            'is_active' => true,
            'is_admin_panel_active' => false,
        ]);

        $this->assertEquals('example.com:8080', $domain->host);
        $this->assertEquals(Protocol::HTTPS, $domain->protocol);
        $this->assertTrue($domain->is_active);
        $this->assertFalse($domain->is_admin_panel_active);
    }

    #[Test]
    public function it_has_links_relationship(): void
    {
        // Arrange
        $domain = Domain::factory()->create();
        $link = Link::factory()->create();

        // Act
        $domain->links()->attach($link);

        // Assert
        $this->assertTrue($domain->links->contains($link));
        $this->assertEquals(1, $domain->links->count());
    }

    #[Test]
    public function it_can_be_converted_to_string(): void
    {
        // Arrange & Act
        $domain = Domain::factory()->create([
            'host' => 'example.com',
        ]);

        // Assert
        $this->assertEquals('example.com', (string) $domain);
    }

    #[Test]
    public function it_has_active_scope(): void
    {
        // Arrange
        Domain::factory()->create(['is_active' => false]);

        // Act
        $activeDomains = Domain::available()->get();

        // Assert
        $this->assertEquals(1, $activeDomains->count());
        $this->assertTrue($activeDomains->first()->is_active);
    }

    #[Test]
    public function it_has_host_without_port_attribute()
    {
        $domain = Domain::factory()->create([
            'host' => 'example.com:8080',
        ]);

        $this->assertEquals('example.com', $domain->hostWithoutPort);
    }

    #[Test]
    public function it_has_host_without_port_attribute_when_no_port_is_present()
    {
        // Create a domain without a port in the host
        $domain = new Domain;
        $domain->host = 'example.com';
        $domain->protocol = 'https';
        $domain->is_active = true;
        $domain->is_admin_panel_active = true; // Set to true to avoid validation issues
        $domain->save();

        // Verify that hostWithoutPort returns the full host when no port is present
        $this->assertEquals('example.com', $domain->hostWithoutPort);
    }

    #[Test]
    public function it_has_updated_at_null_constant()
    {
        $this->assertEquals(null, Domain::UPDATED_AT);
    }

    #[Test]
    public function it_has_guarded_attributes()
    {
        $domain = new Domain;

        $this->assertEquals([], $domain->getGuarded());
    }

    #[Test]
    public function it_requires_at_least_one_active_domain_with_admin_panel()
    {
        // Get the default domain created in setUp
        $defaultDomain = Domain::where('host', 'default-test-domain.com')->first();

        // Create another domain
        $domain = Domain::factory()->create([
            'host' => 'another-domain.com',
            'is_active' => false,
            'is_admin_panel_active' => false,
        ]);

        // Try to update the default domain to inactive or without admin panel
        $this->expectException(ValidationException::class);

        $defaultDomain->is_admin_panel_active = false;
        $defaultDomain->save();
    }

    #[Test]
    public function it_casts_protocol_to_enum()
    {
        $domain = Domain::factory()->create([
            'protocol' => Protocol::HTTPS,
        ]);

        $this->assertInstanceOf(Protocol::class, $domain->protocol);
        $this->assertEquals(Protocol::HTTPS, $domain->protocol);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Create an active domain with admin panel to satisfy validation
        Domain::factory()->create([
            'host' => 'default-test-domain.com',
            'is_active' => true,
            'is_admin_panel_active' => true,
        ]);
    }
}
