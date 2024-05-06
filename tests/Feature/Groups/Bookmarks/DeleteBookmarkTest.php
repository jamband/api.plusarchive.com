<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Bookmarks;

use App\Groups\Bookmarks\BookmarkFactory;
use App\Groups\BookmarkTags\BookmarkTag;
use App\Groups\BookmarkTags\BookmarkTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteBookmarkTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private BookmarkFactory $bookmarkFactory;
    private BookmarkTag $tag;
    private BookmarkTagFactory $tagFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->bookmarkFactory = new BookmarkFactory();
        $this->tag = new BookmarkTag();
        $this->tagFactory = new BookmarkTagFactory();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->delete('/bookmarks/1')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->delete('/bookmarks/1')
            ->assertUnauthorized();
    }

    public function testNotFound(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->delete('/bookmarks/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testDeleteBookmark(): void
    {
        $bookmark = $this->bookmarkFactory
            ->createOne();

        $this->assertDatabaseCount($bookmark::class, 1);

        $this->actingAs($this->userFactory->makeOne())
            ->delete('/bookmarks/'.$bookmark->id)
            ->assertNoContent();

        $this->assertDatabaseCount($bookmark::class, 0);
    }

    public function testDeleteBookmarkWithTags(): void
    {
        $bookmark = $this->bookmarkFactory
            ->createOne();

        $pivotTable = $bookmark->tags()->getTable();

        $this->tagFactory
            ->count(2)
            ->create();

        $bookmark->tags()->sync([1, 2]);

        $this->assertDatabaseCount($bookmark::class, 1)
            ->assertDatabaseCount($this->tag::class, 2)
            ->assertDatabaseCount($pivotTable, 2);

        $this->actingAs($this->userFactory->makeOne())
            ->delete('/bookmarks/'.$bookmark->id)
            ->assertNoContent();

        $this->assertDatabaseCount($bookmark::class, 0)
            ->assertDatabaseCount($this->tag::class, 2)
            ->assertDatabaseCount($pivotTable, 0);
    }
}
