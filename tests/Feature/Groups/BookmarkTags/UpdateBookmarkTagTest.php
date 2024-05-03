<?php


declare(strict_types=1);

namespace Tests\Feature\Groups\BookmarkTags;

use App\Groups\BookmarkTags\BookmarkTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UpdateBookmarkTagTest extends TestCase
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
            ->put('/bookmark-tags/1')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->put('/bookmark-tags/1')
            ->assertUnauthorized();
    }

    public function testModelNotFound(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->put('/bookmark-tags/1', [
                'name' => 'foo',
            ])
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testUpdateBookmarkTag(): void
    {
        $tag = $this->tagFactory
            ->createOne();

        $this->assertDatabaseCount($tag::class, 1);

        $this->actingAs($this->userFactory->makeOne())
            ->put('/bookmark-tags/'.$tag->id, [
                'name' => 'updated_tag1',
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($tag) {
                $json->where('id', $tag->id)
                    ->where('name', 'updated_tag1');
            });

        $this->assertDatabaseCount($tag::class, 1)
            ->assertDatabaseHas($tag::class, [
                'id' => $tag->id,
                'name' => 'updated_tag1',
            ]);
    }
}
