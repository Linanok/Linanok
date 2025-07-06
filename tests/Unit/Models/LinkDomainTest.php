<?php

namespace Models;

use App\Models\Domain;
use App\Models\Link;
use App\Models\LinkDomain;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LinkDomainTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_link_domain()
    {
        $link = Link::factory()->create();
        $domain = Domain::factory()->create();

        $linkDomain = new LinkDomain;
        $linkDomain->link_id = $link->id;
        $linkDomain->domain_id = $domain->id;
        $linkDomain->save();

        $this->assertDatabaseHas('link_domain', [
            'link_id' => $link->id,
            'domain_id' => $domain->id,
        ]);
    }

    #[Test]
    public function it_belongs_to_a_link()
    {
        $link = Link::factory()->create();
        $domain = Domain::factory()->create();

        $linkDomain = new LinkDomain;
        $linkDomain->link_id = $link->id;
        $linkDomain->domain_id = $domain->id;
        $linkDomain->save();

        $this->assertEquals($link->id, $linkDomain->link->id);
    }

    #[Test]
    public function it_belongs_to_a_domain()
    {
        $link = Link::factory()->create();
        $domain = Domain::factory()->create();

        $linkDomain = new LinkDomain;
        $linkDomain->link_id = $link->id;
        $linkDomain->domain_id = $domain->id;
        $linkDomain->save();

        $this->assertEquals($domain->id, $linkDomain->domain->id);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Create an active domain with admin panel to satisfy validation
        Domain::factory()->create([
            'host' => 'default-test-domain.com',
            'is_active' => true,
            'is_admin_panel_available' => true,
        ]);
    }
}
