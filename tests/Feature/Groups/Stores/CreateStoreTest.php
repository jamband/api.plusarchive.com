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

class CreateStoreTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private CountryFactory $countryFactory;
    private Store $store;
    private StoreTag $tag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->countryFactory = new CountryFactory();
        $this->store = new Store();
        $this->tag = new StoreTag();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->post('/stores')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->post('/stores')
            ->assertUnauthorized();
    }

    public function testCreateStore(): void
    {
        $country = $this->countryFactory
            ->createOne();

        $this->assertDatabaseCount($this->store::class, 0);

        $this->actingAs($this->userFactory->makeOne())
            ->post('/stores', [
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

        $this->assertDatabaseCount($this->store::class, 1)
            ->assertDataBaseHas($this->store::class, [
                'id' => 1,
                'name' => 'store1',
                'country_id' => $country->id,
                'url' => 'https://url1.dev',
                'links' => "https://link1.dev\nhttps://link2.dev",
            ]);
    }

    public function testCreateStoreWithSomeEmptyAttributeValues(): void
    {
        $country = $this->countryFactory
            ->createOne();

        $this->actingAs($this->userFactory->makeOne())
            ->post('/stores', [
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

        $this->assertDatabaseHas($this->store::class, [
            'id' => 1,
            'name' => 'store1',
            'country_id' => $country->id,
            'url' => 'https://url1.dev',
            'links' => '',
        ]);
    }

    public function testCreateStoreWithTags(): void
    {
        $pivotTable = $this->store->tags()->getTable();

        $country = $this->countryFactory
            ->createOne();

        $this->actingAs($this->userFactory->makeOne())
            ->post('/stores', [
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

        $this->assertDatabaseCount($this->tag::class, 2)
            ->assertDatabaseHas($this->tag::class, ['name' => 'tag1'])
            ->assertDatabaseHas($this->tag::class, ['name' => 'tag2']);

        $this->assertDatabaseCount($pivotTable, 2)
            ->assertDatabaseHas($pivotTable, ['store_id' => 1, 'tag_id' => 1])
            ->assertDatabaseHas($pivotTable, ['store_id' => 1, 'tag_id' => 2]);
    }
}
