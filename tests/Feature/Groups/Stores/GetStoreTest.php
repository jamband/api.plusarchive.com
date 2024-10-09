<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Stores;

use App\Groups\Stores\StoreFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetStoreTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private StoreFactory $storeFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->storeFactory = new StoreFactory();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->get('/stores/1')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->get('/stores/1')
            ->assertUnauthorized();
    }

    public function testNotFound(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->get('/stores/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testGetStore(): void
    {
        $store = $this->storeFactory
            ->createOne();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/stores/'.$store->id)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($store) {
                $json->where('id', $store->id)
                    ->where('name', $store->name)
                    ->where('country', $store->country->name)
                    ->where('url', $store->url)
                    ->where('links', $store->links)
                    ->where('tags', [])
                    ->where('created_at', $store->created_at->format('Y-m-d H:i'))
                    ->where('updated_at', $store->updated_at->format('Y-m-d H:i'));
            });
    }
}
