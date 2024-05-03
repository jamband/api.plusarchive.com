<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Countries;

use App\Groups\Countries\Country;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CreateCountryTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private Country $country;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->country = new Country();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->post('/countries')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->post('/countries')
            ->assertUnauthorized();
    }

    public function testCreateCountry(): void
    {
        $this->assertDatabaseCount($this->country::class, 0);

        $this->actingAs($this->userFactory->makeOne())
            ->post('/countries', [
                'name' => 'country1',
            ])
            ->assertCreated()
            ->assertHeader(
                'Location',
                $this->app['config']['app.url'].'/countries/1'
            )->assertJson(fn (AssertableJson $json) => $json
                ->where('id', 1)
                ->where('name', 'country1'));

        $this->assertDatabaseCount($this->country::class, 1)
            ->assertDatabaseHas($this->country::class, [
                'id' => 1,
                'name' => 'country1',
            ]);
    }
}
