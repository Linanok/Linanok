<?php

namespace Tests\Unit\Helpers;

use App\Models\Domain;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HelpersTest extends TestCase
{
    use RefreshDatabase;

    protected Domain $domain;

    #[Test]
    public function current_domain_returns_domain_matching_http_host(): void
    {
        // Arrange
        $this->app->instance('request', Request::create('https://example.com'));

        // Act
        $result = current_domain();

        // Assert
        $this->assertInstanceOf(Domain::class, $result);
        $this->assertEquals($this->domain->id, $result->id);
        $this->assertEquals('example.com', $result->host);
        $this->assertEquals('https', $result->protocol->value);
    }

    #[Test]
    public function current_domain_returns_domain_with_port_in_host(): void
    {
        // Arrange
        $domainWithPort = Domain::factory()->create([
            'host' => 'example.com:8080',
            'protocol' => 'https',
            'is_active' => true,
            'is_admin_panel_available' => true,
        ]);

        $this->app->instance('request', Request::create('https://example.com:8080'));

        // Act
        $result = current_domain();

        // Assert
        $this->assertInstanceOf(Domain::class, $result);
        $this->assertEquals($domainWithPort->id, $result->id);
        $this->assertEquals('example.com:8080', $result->host);
    }

    #[Test]
    public function current_domain_returns_null_when_no_matching_domain_exists(): void
    {
        // Arrange
        $this->app->instance('request', Request::create('https://nonexistent-domain.com'));

        // Act
        $result = current_domain();

        // Assert
        $this->assertNull($result);
    }

    #[Test]
    public function current_domain_returns_correct_domain_when_multiple_domains_exist(): void
    {
        // Arrange
        Domain::factory()->create([
            'host' => 'example1.com',
            'protocol' => 'https',
            'is_active' => true,
            'is_admin_panel_available' => true,
        ]);

        $targetDomain = Domain::factory()->create([
            'host' => 'example2.com',
            'protocol' => 'https',
            'is_active' => true,
            'is_admin_panel_available' => true,
        ]);

        Domain::factory()->create([
            'host' => 'example3.com',
            'protocol' => 'https',
            'is_active' => true,
            'is_admin_panel_available' => true,
        ]);

        $this->app->instance('request', Request::create('https://example2.com'));

        // Act
        $result = current_domain();

        // Assert
        $this->assertInstanceOf(Domain::class, $result);
        $this->assertEquals($targetDomain->id, $result->id);
        $this->assertEquals('example2.com', $result->host);
        $this->assertEquals('https', $result->protocol->value);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test domain
        $this->domain = Domain::factory()->create([
            'host' => 'example.com',
            'protocol' => 'https',
            'is_active' => true,
            'is_admin_panel_available' => true,
        ]);
    }
}
