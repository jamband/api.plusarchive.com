<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Stores;

use App\Groups\Stores\StoreFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class GetStoreTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('GET /stores/1');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('GET /stores/1');
    }

    public function testModelNotFound(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/stores/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testGetStore(): void
    {
        $store = StoreFactory::new()
            ->createOne();

        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/stores/'.$store->id)
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
