<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Countries;

use App\Groups\Countries\CountryFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetCountriesTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private CountryFactory $countryFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->countryFactory = new CountryFactory();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->get('/countries')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->get('/countries')
            ->assertUnauthorized();
    }

    public function testGetCountries(): void
    {
        $this->countryFactory
            ->count(5)
            ->state(new Sequence(
                ['name' => 'Unknown'],
                ['name' => 'Worldwide'],
                ['name' => 'Foo'],
                ['name' => 'Bar'],
                ['name' => 'Baz'],
            ))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/countries')
            ->assertOk()
            ->assertExactJson(['Unknown', 'Worldwide', 'Bar', 'Baz', 'Foo']);
    }
}
