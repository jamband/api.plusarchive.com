<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Countries;

use App\Groups\Countries\CountryFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteCountryTest extends TestCase
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
            ->delete('/countries/1')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->delete('/countries/1')
            ->assertUnauthorized();
    }

    public function testNotFound(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->delete('/countries/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testDeleteCountry(): void
    {
        $country = $this->countryFactory
            ->createOne();

        $this->assertDatabaseCount($country::class, 1);

        $this->actingAs($this->userFactory->makeOne())
            ->delete('/countries/'.$country->id)
            ->assertNoContent();

        $this->assertDatabaseCount($country::class, 0);
    }
}
