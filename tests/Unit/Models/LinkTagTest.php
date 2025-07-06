<?php

namespace Models;

use App\Models\Link;
use App\Models\LinkTag;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LinkTagTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_link_tag()
    {
        $link = Link::factory()->create();
        $tag = Tag::factory()->create();

        $linkTag = new LinkTag;
        $linkTag->link_id = $link->id;
        $linkTag->tag_id = $tag->id;
        $linkTag->save();

        $this->assertDatabaseHas('link_tags', [
            'link_id' => $link->id,
            'tag_id' => $tag->id,
        ]);
    }

    #[Test]
    public function it_has_only_created_at_timestamp()
    {
        $link = Link::factory()->create();
        $tag = Tag::factory()->create();

        $linkTag = new LinkTag;
        $linkTag->link_id = $link->id;
        $linkTag->tag_id = $tag->id;
        $linkTag->save();

        $this->assertNotNull($linkTag->created_at);
        $this->assertNull($linkTag->updated_at);
    }
}
