<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Stores;

use App\Groups\Countries\CountryFactory;
use App\Groups\Stores\Store;
use App\Groups\Stores\StoreFactory;
use App\Groups\StoreTags\StoreTagFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetStoresTest extends TestCase
{
    use RefreshDatabase;

    public function testGetStores(): void
    {
        /** @var array<int, Store> $stores */
        $stores = StoreFactory::new()
            ->count(2)
            ->hasAttached(
                factory: StoreTagFactory::new()
                    ->count(2),
                relationship: 'tags',
            )
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => (new Carbon())->addMinutes($sequence->index),
            ]))
            ->create();

        $this->getJson('/stores')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($stores) {
                $json->where('data.0', [
                    'name' => $stores[1]->name,
                    'url' => $stores[1]->url,
                    'links' => $stores[1]->links,
                    'country' => $stores[1]->country->name,
                    'tags' => [
                        $stores[1]->tags[0]->name,
                        $stores[1]->tags[1]->name,
                    ],
                ]);

                $json->where('data.1', [
                    'name' => $stores[0]->name,
                    'url' => $stores[0]->url,
                    'links' => $stores[0]->links,
                    'country' => $stores[0]->country->name,
                    'tags' => [
                        $stores[0]->tags[0]->name,
                        $stores[0]->tags[1]->name,
                    ],
                ]);

                $json->where('pagination', [
                    'currentPage' => 1,
                    'lastPage' => 1,
                    'perPage' => 14,
                    'total' => 2,
                ]);
            });
    }

    public function testGetStoresWithCountry(): void
    {
        StoreFactory::new()
            ->for(
                CountryFactory::new()->state([
                    'name' => 'foo',
                ]),
            )
            ->createOne();

        /** @var array<int, Store> $stores */
        $stores = StoreFactory::new()
            ->count(2)
            ->for(
                CountryFactory::new()->state([
                    'name' => 'bar',
                ]),
            )
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => (new Carbon())->addMinutes($sequence->index),
            ]))
            ->create();

        $this->getJson('/stores?country=bar')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($stores) {
                $json->where('data.0', [
                    'name' => $stores[1]->name,
                    'url' => $stores[1]->url,
                    'links' => $stores[1]->links,
                    'country' => 'bar',
                    'tags' => [],
                ])
                ->where('data.1', [
                    'name' => $stores[0]->name,
                    'url' => $stores[0]->url,
                    'links' => $stores[0]->links,
                    'country' => 'bar',
                    'tags' => [],
                ])
                ->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetStoresWithUnmatchedCountry(): void
    {
        StoreFactory::new()
            ->for(
                CountryFactory::new()->state([
                    'name' => 'foo',
                ]),
            )
            ->createOne();

        $this->getJson('/stores?country=bar')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data', [])
                ->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 0)
                    ->etc()));
    }

    public function testGetStoresWithTag(): void
    {
        StoreFactory::new()
            ->hasAttached(
                factory: StoreTagFactory::new()
                    ->state(['name' => 'foo']),
                relationship: 'tags',
            )
            ->createOne();

        /** @var array<int, Store> $stores */
        $stores = StoreFactory::new()
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => (new Carbon())->addMinutes($sequence->index),
            ]))
            ->create();

        StoreTagFactory::new()
            ->state(['name' => 'bar'])
            ->createOne();

        $stores[0]->tags()->sync([2]);
        $stores[1]->tags()->sync([2]);

        $this->getJson('/stores?tag=bar')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($stores) {
                $json->where('data.0', [
                    'name' => $stores[1]->name,
                    'url' => $stores[1]->url,
                    'links' => $stores[1]->links,
                    'country' => $stores[1]->country->name,
                    'tags' => ['bar'],
                ]);

                $json->where('data.1', [
                    'name' => $stores[0]->name,
                    'url' => $stores[0]->url,
                    'links' => $stores[0]->links,
                    'country' => $stores[0]->country->name,
                    'tags' => ['bar'],
                ]);

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetStoresWithUnmatchedTag(): void
    {
        StoreFactory::new()
            ->hasAttached(
                factory: StoreTagFactory::new()
                    ->state(['name' => 'foo']),
                relationship: 'tags',
            )
            ->createOne();

        $this->getJson('/stores?tag=bar')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data', [])
                ->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 0)
                    ->etc()));
    }

    public function testGetStoresWithCountryAndTag(): void
    {
        CountryFactory::new()
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'name' => 'country'.($sequence->index + 1),
            ]))
            ->create();

        /** @var array<int, Store> $stores */
        $stores = StoreFactory::new()
            ->count(4)
            ->state(new Sequence(
                ['country_id' => 1],
                ['country_id' => 1],
                ['country_id' => 1],
                ['country_id' => 2],
            ))
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => (new Carbon())->addMinutes($sequence->index),
            ]))
            ->create();

        StoreTagFactory::new()
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'name' => 'tag'.($sequence->index + 1),
            ]))
            ->create();

        $stores[0]->tags()->sync([1]);
        $stores[1]->tags()->sync([1]);

        $this->getJson('/stores?country=country1&tag=tag1')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($stores) {
                $json->where('data.0', [
                    'name' => $stores[1]->name,
                    'url' => $stores[1]->url,
                    'links' => $stores[1]->links,
                    'country' => 'country1',
                    'tags' => ['tag1'],
                ]);

                $json->where('data.1', [
                    'name' => $stores[0]->name,
                    'url' => $stores[0]->url,
                    'links' => $stores[0]->links,
                    'country' => 'country1',
                    'tags' => ['tag1'],
                ]);

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testQueryStringTypes(): void
    {
        $this->getJson('/stores?country[]=&tag[]=')
            ->assertOk();
    }
}
