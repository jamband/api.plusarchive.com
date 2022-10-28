<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\BookmarkTags;

use App\Groups\BookmarkTags\BookmarkTag;
use App\Groups\BookmarkTags\BookmarkTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestMiddleware;

class DeleteBookmarkTagTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('DELETE /bookmark-tags/1');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('DELETE /bookmark-tags/1');
    }

    public function testModelNotFound(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/bookmark-tags/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testDeleteBookmarkTag(): void
    {
        $tag = BookmarkTagFactory::new()
            ->createOne();

        $this->assertDatabaseCount(BookmarkTag::class, 1);

        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/bookmark-tags/'.$tag->id)
            ->assertNoContent();

        $this->assertDatabaseCount(BookmarkTag::class, 0);
    }
}
