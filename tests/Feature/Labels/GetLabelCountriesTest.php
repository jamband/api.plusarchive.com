<?php

declare(strict_types=1);

namespace Tests\Feature\Labels;

use App\Groups\Countries\CountryFactory;
use App\Groups\Labels\LabelFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetLabelCountriesTest extends TestCase
{
    use RefreshDatabase;

    public function testGetLabelCountries(): void
    {
        CountryFactory::new()
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        LabelFactory::new()
            ->count(5)
            ->state(new Sequence(
                ['country_id' => 1],
                ['country_id' => 2],
                ['country_id' => 2],
                ['country_id' => 3],
                ['country_id' => 3],
            ))
            ->create();

        $this->getJson('/labels/countries')
            ->assertOk()
            ->assertExactJson(['bar', 'baz', 'foo']);
    }
}
