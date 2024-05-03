<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\BookmarkTags;

use App\Groups\BookmarkTags\BookmarkTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetBookmarkTagTest extends TestCase
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
            ->get('/bookmark-tags/1')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->get('/bookmark-tags/1')
            ->assertUnauthorized();
    }

    public function testModelNotFound(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->get('/bookmark-tags/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testGetBookmarkTag(): void
    {
        $tag = $this->tagFactory
            ->createOne();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/bookmark-tags/'.$tag->id)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($tag) {
                $json->where('id', $tag->id)
                    ->where('name', $tag->name);
            });
    }
}
