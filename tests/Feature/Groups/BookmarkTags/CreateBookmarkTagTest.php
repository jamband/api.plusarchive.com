<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\BookmarkTags;

use App\Groups\BookmarkTags\BookmarkTag;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CreateBookmarkTagTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private BookmarkTag $tag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->tag = new BookmarkTag();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->post('/bookmark-tags')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->post('/bookmark-tags')
            ->assertUnauthorized();
    }

    public function testCreateBookmarkTag(): void
    {
        $this->assertDatabaseCount($this->tag::class, 0);

        $this->actingAs($this->userFactory->makeOne())
            ->post('/bookmark-tags', [
                'name' => 'tag1',
            ])
            ->assertCreated()
            ->assertHeader(
                'Location',
                $this->app['config']['app.url'].'/bookmark-tags/1'
            )
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('id', 1)
                ->where('name', 'tag1'));

        $this->assertDatabaseCount($this->tag::class, 1)
            ->assertDatabaseHas(BookmarkTag::class, [
                'id' => 1,
                'name' => 'tag1',
            ]);
    }
}
