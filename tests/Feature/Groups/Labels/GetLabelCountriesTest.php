<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Labels;

use App\Groups\Countries\CountryFactory;
use App\Groups\Labels\LabelFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetLabelCountriesTest extends TestCase
{
    use RefreshDatabase;

    private CountryFactory $countryFactory;
    private LabelFactory $labelFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->countryFactory = new CountryFactory();
        $this->labelFactory = new LabelFactory();
    }

    public function testGetLabelCountries(): void
    {
        $this->countryFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->labelFactory
            ->count(5)
            ->state(new Sequence(
                ['country_id' => 1],
                ['country_id' => 2],
                ['country_id' => 2],
                ['country_id' => 3],
                ['country_id' => 3],
            ))
            ->create();

        $this->get('/labels/countries')
            ->assertOk()
            ->assertExactJson(['bar', 'baz', 'foo']);
    }
}
