<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Stores;

use App\Groups\Countries\CountryFactory;
use App\Groups\Stores\Store;
use App\Groups\StoreTags\StoreTag;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class CreateStoreTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('POST /stores');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('POST /stores');
    }

    public function testCreateStore(): void
    {
        $country = CountryFactory::new()
            ->createOne();

        $this->assertDatabaseCount(Store::class, 0);

        $this->actingAs(UserFactory::new()->makeOne())
            ->postJson('/stores', [
                'name' => 'store1',
                'country' => $country->name,
                'url' => 'https://url1.dev',
                'links' => "https://link1.dev\nhttps://link2.dev",
            ])
            ->assertCreated()
            ->assertHeader('Location', $this->app['config']['app.url'].'/stores/1')
            ->assertJson(function (AssertableJson $json) use ($country) {
                $json->where('id', 1)
                    ->where('name', 'store1')
                    ->where('country', $country->name)
                    ->where('url', 'https://url1.dev')
                    ->where('links', "https://link1.dev\nhttps://link2.dev")
                    ->where('tags', [])
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseCount(Store::class, 1);

        $this->assertDataBaseHas(Store::class, [
            'id' => 1,
            'name' => 'store1',
            'country_id' => $country->id,
            'url' => 'https://url1.dev',
            'links' => "https://link1.dev\nhttps://link2.dev",
        ]);
    }

    public function testCreateStoreWithSomeEmptyAttributeValues(): void
    {
        $country = CountryFactory::new()
            ->createOne();

        $this->actingAs(UserFactory::new()->makeOne())
            ->postJson('/stores', [
                'name' => 'store1',
                'country' => $country->name,
                'url' => 'https://url1.dev',
            ])
            ->assertCreated()
            ->assertJson(function (AssertableJson $json) use ($country) {
                $json->where('id', 1)
                    ->where('name', 'store1')
                    ->where('country', $country->name)
                    ->where('url', 'https://url1.dev')
                    ->where('links', '')
                    ->where('tags', [])
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseHas(Store::class, [
            'id' => 1,
            'name' => 'store1',
            'country_id' => $country->id,
            'url' => 'https://url1.dev',
            'links' => '',
        ]);
    }

    public function testCreateStoreWithTags(): void
    {
        $store = new Store();
        $pivotTable = $store->tags()->getTable();

        $country = CountryFactory::new()
            ->createOne();

        $this->actingAs(UserFactory::new()->makeOne())
            ->postJson('/stores', [
                'name' => 'store1',
                'country' => $country->name,
                'url' => 'https://url1.dev',
                'tags' => ['tag1', 'tag2'],
            ])
            ->assertCreated()
            ->assertJson(function (AssertableJson $json) use ($country) {
                $json->where('id', 1)
                    ->where('name', 'store1')
                    ->where('country', $country->name)
                    ->where('url', 'https://url1.dev')
                    ->where('links', '')
                    ->where('tags', ['tag1', 'tag2'])
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseCount(StoreTag::class, 2);
        $this->assertDatabaseHas(StoreTag::class, ['name' => 'tag1']);
        $this->assertDatabaseHas(StoreTag::class, ['name' => 'tag2']);

        $this->assertDatabaseCount($pivotTable, 2);
        $this->assertDatabaseHas($pivotTable, ['store_id' => 1, 'tag_id' => 1]);
        $this->assertDatabaseHas($pivotTable, ['store_id' => 1, 'tag_id' => 2]);
    }
}
