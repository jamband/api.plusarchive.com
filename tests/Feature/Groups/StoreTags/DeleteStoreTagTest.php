<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\StoreTags;

use App\Groups\StoreTags\StoreTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteStoreTagTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private StoreTagFactory $tagFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->tagFactory = new StoreTagFactory();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->delete('/store-tags/1')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->delete('/store-tags/1')
            ->assertUnauthorized();
    }

    public function testNotFound(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->delete('/store-tags/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testDeleteStoreTag(): void
    {
        $tag = $this->tagFactory
            ->createOne();

        $this->assertDatabaseCount($tag::class, 1);

        $this->actingAs($this->userFactory->makeOne())
            ->delete('/store-tags/'.$tag->id)
            ->assertNoContent();

        $this->assertDatabaseCount($tag::class, 0);
    }
}
