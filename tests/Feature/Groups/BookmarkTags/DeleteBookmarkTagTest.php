<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\BookmarkTags;

use App\Groups\BookmarkTags\BookmarkTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteBookmarkTagTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private BookmarkTagFactory $tagFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->tagFactory = new BookmarkTagFactory();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->delete('/bookmark-tags/1')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->delete('/bookmark-tags/1')
            ->assertUnauthorized();
    }

    public function testNotFound(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->delete('/bookmark-tags/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testDeleteBookmarkTag(): void
    {
        $tag = $this->tagFactory
            ->createOne();

        $this->assertDatabaseCount($tag::class, 1);

        $this->actingAs($this->userFactory->makeOne())
            ->delete('/bookmark-tags/'.$tag->id)
            ->assertNoContent();

        $this->assertDatabaseCount($tag::class, 0);
    }
}
