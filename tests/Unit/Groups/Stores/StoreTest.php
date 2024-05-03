<?php

declare(strict_types=1);

namespace Tests\Unit\Groups\Stores;

use App\Groups\Countries\CountryFactory;
use App\Groups\Stores\Store;
use App\Groups\Stores\StoreFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    private Store $store;
    private StoreFactory $storeFactory;
    private CountryFactory $countryFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = new Store();
        $this->storeFactory = new StoreFactory();
        $this->countryFactory = new CountryFactory();
    }

    public function testTimestamps(): void
    {
        $this->assertTrue($this->store->timestamps);
    }

    public function testCountry(): void
    {
        $relation = $this->store->country();
        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame('country_id', $relation->getForeignKeyName());
    }

    public function testTags(): void
    {
        $pivot = $this->store->tags();

        $this->assertInstanceOf(BelongsToMany::class, $pivot);
        $this->assertSame('tag_store', $pivot->getTable());
        $this->assertSame('stores', $pivot->getParent()->getTable());
        $this->assertSame('store_tags', $pivot->getRelated()->getTable());
        $this->assertSame('tag_id', $pivot->getRelatedPivotKeyName());
        $this->assertSame('store_id', $pivot->getForeignPivotKeyName());
    }

    public function testGetCountryNames(): void
    {
        $this->countryFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->storeFactory
            ->count(5)
            ->state(new Sequence(
                ['country_id' => 1],
                ['country_id' => 2],
                ['country_id' => 2],
                ['country_id' => 3],
                ['country_id' => 3],
            ))
            ->create();

        $this->assertSame(['bar', 'baz', 'foo'], $this->store->getCountryNames());
    }
}
