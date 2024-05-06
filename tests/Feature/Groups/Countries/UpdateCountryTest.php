<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Countries;

use App\Groups\Countries\CountryFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UpdateCountryTest extends TestCase
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
            ->put('/countries/1')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->put('/countries/1')
            ->assertUnauthorized();
    }

    public function testNotFound(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->put('/countries/1', [
                'name' => 'foo',
            ])
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testUpdateCountry(): void
    {
        $country = $this->countryFactory
            ->createOne();

        $this->assertDatabaseCount($country::class, 1);

        $this->actingAs($this->userFactory->makeOne())
            ->put('/countries/'.$country->id, [
                'name' => 'updated_country1',
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($country) {
                $json->where('id', $country->id);
                $json->where('name', 'updated_country1');
            });

        $this->assertDatabaseCount($country::class, 1)
            ->assertDatabaseHas($country::class, [
                'id' => $country->id,
                'name' => 'updated_country1',
            ]);
    }
}
