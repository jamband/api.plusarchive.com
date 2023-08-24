<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Stores;

use App\Groups\Countries\CountryFactory;
use App\Groups\Stores\Store;
use App\Groups\Stores\StoreFactory;
use App\Groups\StoreTags\StoreTag;
use App\Groups\StoreTags\StoreTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class UpdateStoreTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('PUT /stores/1');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('PUT /stores/1');
    }

    public function testModelNotFound(): void
    {
        $country = CountryFactory::new()
            ->createOne();

        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/stores/1', [
                'name' => 'store1',
                'country' => $country->name,
                'url' => 'https://url1.dev',
            ])
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testUpdateStore(): void
    {
        $store = StoreFactory::new()
            ->createOne();

        $this->assertDatabaseCount(Store::class, 1);

        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/stores/'.$store->id, [
                'name' => 'updated_store1',
                'country' => $store->country->name,
                'url' => 'https://updated-url1.dev',
                'links' => "https://updated-link1.dev\nhttps://updated-link2.dev",
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($store) {
                $json->where('id', $store->id)
                    ->where('name', 'updated_store1')
                    ->where('country', $store->country->name)
                    ->where('url', 'https://updated-url1.dev')
                    ->where('links', "https://updated-link1.dev\nhttps://updated-link2.dev")
                    ->where('tags', [])
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseCount(Store::class, 1);

        $this->assertDatabaseHas(Store::class, [
            'id' => $store->id,
            'name' => 'updated_store1',
            'country_id' => 1,
            'url' => 'https://updated-url1.dev',
            'links' => "https://updated-link1.dev\nhttps://updated-link2.dev",
        ]);
    }

    public function testUpdateStoreWithSomeEmptyAttributeValues(): void
    {
        $store = StoreFactory::new()
            ->createOne();

        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/stores/'.$store->id, [
                'name' => 'updated_store1',
                'country' => $store->country->name,
                'url' => 'https://updated-url1.dev',
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($store) {
                $json->where('id', $store->id)
                    ->where('name', 'updated_store1')
                    ->where('country', $store->country->name)
                    ->where('url', 'https://updated-url1.dev')
                    ->where('links', '')
                    ->where('tags', [])
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseHas(Store::class, [
            'id' => $store->id,
            'name' => 'updated_store1',
            'country_id' => 1,
            'url' => 'https://updated-url1.dev',
            'links' => '',
        ]);
    }

    public function testUpdateStoreWithTags(): void
    {
        $store = StoreFactory::new()
            ->createOne();

        $pivotTable = $store->tags()->getTable();

        StoreTagFactory::new()
            ->count(4)
            ->state(new Sequence(
                ['name' => 'tag1'],
                ['name' => 'tag2'],
                ['name' => 'tag3'],
                ['name' => 'tag4'],
            ))
            ->create();

        $store->tags()->sync([1, 2]);

        $this->assertDatabaseCount(StoreTag::class, 4);

        $this->assertDatabaseCount($pivotTable, 2);
        $this->assertDatabaseHas($pivotTable, ['store_id' => 1, 'tag_id' => 1]);
        $this->assertDatabaseHas($pivotTable, ['store_id' => 1, 'tag_id' => 2]);

        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/stores/'.$store->id, [
                'name' => 'updated_store1',
                'country' => $store->country->name,
                'url' => 'https://updated-url1.dev',
                'tags' => ['tag3', 'tag4'],
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($store) {
                $json->where('id', $store->id)
                    ->where('name', 'updated_store1')
                    ->where('country', $store->country->name)
                    ->where('url', 'https://updated-url1.dev')
                    ->where('links', '')
                    ->where('tags', ['tag3', 'tag4'])
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseCount(StoreTag::class, 4);

        $this->assertDatabaseCount($pivotTable, 2);
        $this->assertDatabaseHas($pivotTable, ['store_id' => 1, 'tag_id' => 3]);
        $this->assertDatabaseHas($pivotTable, ['store_id' => 1, 'tag_id' => 4]);
    }
}
