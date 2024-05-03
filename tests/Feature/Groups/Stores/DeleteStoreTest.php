<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Stores;

use App\Groups\Stores\StoreFactory;
use App\Groups\StoreTags\StoreTag;
use App\Groups\StoreTags\StoreTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteStoreTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private StoreFactory $storeFactory;
    private StoreTagFactory $tagFactory;
    private StoreTag $tag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->storeFactory = new StoreFactory();
        $this->tagFactory = new StoreTagFactory();
        $this->tag = new StoreTag();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->delete('/stores/1')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->delete('/stores/1')
            ->assertUnauthorized();
    }

    public function testModelNotFound(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->delete('/stores/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testDeleteStore(): void
    {
        $store = $this->storeFactory
            ->createOne();

        $this->assertDatabaseCount($store::class, 1);

        $this->actingAs($this->userFactory->makeOne())
            ->delete('/stores/'.$store->id)
            ->assertNoContent();

        $this->assertDatabaseCount($store::class, 0);
    }

    public function testDeleteStoreWithTags(): void
    {
        $store = $this->storeFactory
            ->createOne();

        $pivotTable = $store->tags()->getTable();

        $this->tagFactory
            ->count(2)
            ->create();

        $store->tags()->sync([1, 2]);

        $this->assertDatabaseCount($store::class, 1)
            ->assertDatabaseCount($this->tag::class, 2)
            ->assertDatabaseCount($pivotTable, 2);

        $this->actingAs($this->userFactory->makeOne())
            ->delete('/stores/'.$store->id)
            ->assertNoContent();

        $this->assertDatabaseCount($store::class, 0)
            ->assertDatabaseCount($this->tag::class, 2)
            ->assertDatabaseCount($pivotTable, 0);
    }
}
