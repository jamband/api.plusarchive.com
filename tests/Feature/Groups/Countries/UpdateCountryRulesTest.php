<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Countries;

use App\Groups\Countries\CountryFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class UpdateCountryRulesTest extends TestCase
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

    /**
     * @param array<string, mixed> $data
     * @return TestResponse<Response>
     */
    protected function request(array $data): TestResponse
    {
        return $this->actingAs($this->userFactory->makeOne())
            ->put('/countries/1', $data)
            ->assertUnprocessable();
    }

    public function testNameRequiredRule(): void
    {
        $this->request(['name' => null])
            ->assertJsonPath('errors.name', __('validation.required', [
                'attribute' => 'name',
            ]));
    }

    public function testNameStringRule(): void
    {
        $this->request(['name' => 1])
            ->assertJsonPath('errors.name', __('validation.string', [
                'attribute' => 'name',
            ]));
    }

    public function testNameMaxStringRule(): void
    {
        $this->request(['name' => str_repeat('a', 101)])
            ->assertJsonPath('errors.name', __('validation.max.string', [
                'attribute' => 'name',
                'max' => 100,
            ]));
    }

    public function testNameUniqueRule(): void
    {
        $this->countryFactory
            ->count(2)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
            ))
            ->create();

        $this->request(['name' => 'bar'])
            ->assertJsonPath('errors.name', __('validation.unique', [
                'attribute' => 'name',
            ]));
    }
}
