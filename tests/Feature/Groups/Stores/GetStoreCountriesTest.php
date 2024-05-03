<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Stores;

use App\Groups\Countries\CountryFactory;
use App\Groups\Stores\StoreFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetStoreCountriesTest extends TestCase
{
    use RefreshDatabase;

    private CountryFactory $countryFactory;
    private StoreFactory $storeFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->countryFactory = new CountryFactory();
        $this->storeFactory = new StoreFactory();
    }

    public function testGetStoreCountries(): void
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

        $this->get('/stores/countries')
            ->assertOk()
            ->assertExactJson(['bar', 'baz', 'foo']);
    }
}
