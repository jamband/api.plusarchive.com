<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\BookmarkTags;

use App\Groups\BookmarkTags\BookmarkTag;
use App\Groups\BookmarkTags\BookmarkTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class UpdateBookmarkTagTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('PUT /bookmark-tags/1');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('PUT /bookmark-tags/1');
    }

    public function testModelNotFound(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/bookmark-tags/1', [
                'name' => 'foo',
            ])
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testUpdateBookmarkTag(): void
    {
        $tag = BookmarkTagFactory::new()
            ->createOne();

        $this->assertDatabaseCount(BookmarkTag::class, 1);

        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/bookmark-tags/'.$tag->id, [
                'name' => 'updated_tag1',
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($tag) {
                $json->where('id', $tag->id)
                    ->where('name', 'updated_tag1');
            });

        $this->assertDatabaseCount(BookmarkTag::class, 1);

        $this->assertDatabaseHas(BookmarkTag::class, [
            'id' => $tag->id,
            'name' => 'updated_tag1',
        ]);
    }
}
