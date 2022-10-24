<?php

declare(strict_types=1);

namespace Tests\Feature\Stores;

use App\Groups\Countries\CountryFactory;
use App\Groups\Stores\StoreFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetStoreCountriesTest extends TestCase
{
    use RefreshDatabase;

    public function testGetStoreCountries(): void
    {
        CountryFactory::new()
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        StoreFactory::new()
            ->count(5)
            ->state(new Sequence(
                ['country_id' => 1],
                ['country_id' => 2],
                ['country_id' => 2],
                ['country_id' => 3],
                ['country_id' => 3],
            ))
            ->create();

        $this->getJson('/stores/countries')
            ->assertOk()
            ->assertExactJson(['bar', 'baz', 'foo']);
    }
}
