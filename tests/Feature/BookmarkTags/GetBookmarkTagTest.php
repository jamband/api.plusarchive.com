<?php

declare(strict_types=1);

namespace Tests\Feature\BookmarkTags;

use App\Groups\BookmarkTags\BookmarkTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class GetBookmarkTagTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('GET /bookmark-tags/1');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('GET /bookmark-tags/1');
    }

    public function testModelNotFound(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/bookmark-tags/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testGetBookmarkTag(): void
    {
        $tag = BookmarkTagFactory::new()
            ->createOne();

        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/bookmark-tags/'.$tag->id)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($tag) {
                $json->where('id', $tag->id)
                    ->where('name', $tag->name);
            });
    }
}
