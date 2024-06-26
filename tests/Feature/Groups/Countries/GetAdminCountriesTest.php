<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Countries;

use App\Groups\Countries\Country;
use App\Groups\Countries\CountryFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetAdminCountriesTest extends TestCase
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
            ->get('/countries/admin')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->get('/countries/admin')
            ->assertUnauthorized();
    }

    public function testGetAdminCountries(): void
    {
        /** @var array<int, Country> $countries */
        $countries = $this->countryFactory
            ->count(3)
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/countries/admin')
            ->assertOk()
            ->assertJsonCount(3)
            ->assertJson(function (AssertableJson $json) use ($countries) {
                $json->where('0', [
                    'id' => $countries[2]->id,
                    'name' => $countries[2]->name,
                ]);

                $json->where('1', [
                    'id' => $countries[1]->id,
                    'name' => $countries[1]->name,
                ]);

                $json->where('2', [
                    'id' => $countries[0]->id,
                    'name' => $countries[0]->name,
                ]);
            });
    }

    public function testGetAdminCountriesWithSortAsc(): void
    {
        /** @var array<int, Country> $countries */
        $countries = $this->countryFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/countries/admin?sort=name')
            ->assertOk()
            ->assertJsonCount(3)
            ->assertJson(function (AssertableJson $json) use ($countries) {
                $json->has('0', fn (AssertableJson $json) => $json
                    ->where('id', $countries[1]->id)
                    ->etc());
            });
    }

    public function testGetAdminCountriesWithSortDesc(): void
    {
        /** @var array<int, Country> $countries */
        $countries = $this->countryFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/countries/admin?sort=-name')
            ->assertOk()
            ->assertJsonCount(3)
            ->assertJson(function (AssertableJson $json) use ($countries) {
                $json->has('0', fn (AssertableJson $json) => $json
                    ->where('id', $countries[0]->id)
                    ->etc());
            });
    }

    public function testGetAdminCountriesWithName(): void
    {
        /** @var array<int, Country> $countries */
        $countries = $this->countryFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/countries/admin?name=ba')
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJson(function (AssertableJson $json) use ($countries) {
                $json->has('0', fn (AssertableJson $json) => $json
                    ->where('id', $countries[2]->id)
                    ->etc());
            });
    }

    public function testQueryStringTypes(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->get('/countries/admin?name[]=&sort[]=')
            ->assertOk();
    }
}
