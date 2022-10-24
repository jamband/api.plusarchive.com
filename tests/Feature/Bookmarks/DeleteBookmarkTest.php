<?php

declare(strict_types=1);

namespace Tests\Feature\Bookmarks;

use App\Groups\Bookmarks\Bookmark;
use App\Groups\Bookmarks\BookmarkFactory;
use App\Groups\BookmarkTags\BookmarkTag;
use App\Groups\BookmarkTags\BookmarkTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestMiddleware;

class DeleteBookmarkTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('DELETE /bookmarks/1');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('DELETE /bookmarks/1');
    }

    public function testModelNotFound(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/bookmarks/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testDeleteBookmark(): void
    {
        $bookmark = BookmarkFactory::new()
            ->createOne();

        $this->assertDatabaseCount(Bookmark::class, 1);

        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/bookmarks/'.$bookmark->id)
            ->assertNoContent();

        $this->assertDatabaseCount(Bookmark::class, 0);
    }

    public function testDeleteBookmarkWithTags(): void
    {
        $bookmark = BookmarkFactory::new()
            ->createOne();

        $pivotTable = $bookmark->tags()->getTable();

        BookmarkTagFactory::new()
            ->count(2)
            ->create();

        $bookmark->tags()->sync([1, 2]);

        $this->assertDatabaseCount(Bookmark::class, 1);
        $this->assertDatabaseCount(BookmarkTag::class, 2);
        $this->assertDatabaseCount($pivotTable, 2);

        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/bookmarks/'.$bookmark->id)
            ->assertNoContent();

        $this->assertDatabaseCount(Bookmark::class, 0);
        $this->assertDatabaseCount(BookmarkTag::class, 2);
        $this->assertDatabaseCount($pivotTable, 0);
    }
}
