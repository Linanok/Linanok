<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DatabaseCompatibilityTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_connect_to_database(): void
    {
        // Test basic database connectivity
        $this->assertTrue(DB::connection()->getPdo() !== null);
    }

    #[Test]
    public function it_can_create_and_query_users(): void
    {
        // Test User model operations
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $retrievedUser = User::where('email', 'test@example.com')->first();
        $this->assertEquals('Test User', $retrievedUser->name);
    }

    #[Test]
    public function it_can_create_and_query_domains(): void
    {
        // Test Domain model operations
        $domain = Domain::factory()->create([
            'host' => 'example.com',
            'protocol' => 'https',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('domains', [
            'host' => 'example.com',
            'protocol' => 'https',
            'is_active' => true,
        ]);

        $retrievedDomain = Domain::where('host', 'example.com')->first();
        $this->assertEquals('https', $retrievedDomain->protocol->value);
    }

    #[Test]
    public function it_can_create_and_query_links_with_relationships(): void
    {
        // Create a domain first
        $domain = Domain::factory()->create([
            'host' => 'test.com',
            'is_active' => true,
            'is_admin_panel_active' => true,
        ]);

        // Test Link model operations with relationships
        $link = Link::factory()->create([
            'original_url' => 'https://example.com',
            'slug' => 'test-link',
            'is_active' => true,
        ]);

        // Attach domain to link
        $link->domains()->attach($domain);

        $this->assertDatabaseHas('links', [
            'original_url' => 'https://example.com',
            'slug' => 'test-link',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('link_domain', [
            'link_id' => $link->id,
            'domain_id' => $domain->id,
        ]);

        // Test relationship queries
        $retrievedLink = Link::with('domains')->where('slug', 'test-link')->first();
        $this->assertTrue($retrievedLink->domains->contains($domain));
    }

    #[Test]
    public function it_supports_foreign_key_constraints(): void
    {
        // Create a domain
        $domain = Domain::factory()->create([
            'host' => 'constraint-test.com',
            'is_active' => true,
            'is_admin_panel_active' => true,
        ]);

        // Create a link
        $link = Link::factory()->create();
        $link->domains()->attach($domain);

        // Verify the relationship exists
        $this->assertDatabaseHas('link_domain', [
            'link_id' => $link->id,
            'domain_id' => $domain->id,
        ]);

        // Delete the link and verify cascade delete works
        $link->delete();

        $this->assertDatabaseMissing('link_domain', [
            'link_id' => $link->id,
            'domain_id' => $domain->id,
        ]);
    }

    #[Test]
    public function it_supports_boolean_columns(): void
    {
        // Test boolean column handling
        $domain = Domain::factory()->create([
            'host' => 'bool-test.com',
            'is_active' => true,
            'is_admin_panel_active' => false,
        ]);

        $this->assertTrue($domain->is_active);
        $this->assertFalse($domain->is_admin_panel_active);

        // Test boolean queries
        $activeDomains = Domain::where('is_active', true)->count();
        $this->assertGreaterThanOrEqual(1, $activeDomains);
    }

    #[Test]
    public function it_supports_datetime_columns(): void
    {
        // Test datetime column handling
        $now = now();
        $futureDate = $now->copy()->addDay();

        $link = Link::factory()->create([
            'available_at' => $now,
            'unavailable_at' => $futureDate,
        ]);

        $this->assertEquals($now->format('Y-m-d H:i:s'), $link->available_at->format('Y-m-d H:i:s'));
        $this->assertEquals($futureDate->format('Y-m-d H:i:s'), $link->unavailable_at->format('Y-m-d H:i:s'));
    }

    #[Test]
    public function it_supports_text_and_longtext_columns(): void
    {
        // Test text column handling
        $longDescription = str_repeat('This is a long description. ', 100);

        $link = Link::factory()->create([
            'description' => $longDescription,
        ]);

        $this->assertEquals($longDescription, $link->description);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Create a default domain for tests that require it
        if (! Domain::where('host', 'default-test-domain.com')->exists()) {
            Domain::factory()->create([
                'host' => 'default-test-domain.com',
                'is_active' => true,
                'is_admin_panel_active' => true,
            ]);
        }
    }
}
