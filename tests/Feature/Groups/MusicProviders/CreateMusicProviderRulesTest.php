<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\MusicProviders;

use App\Groups\MusicProviders\MusicProviderFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class CreateMusicProviderRulesTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private MusicProviderFactory $providerFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->providerFactory = new MusicProviderFactory();
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function request(array $data): TestResponse
    {
        return $this->actingAs($this->userFactory->makeOne())
            ->post('/music-providers', $data)
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
        $provider = $this->providerFactory
            ->createOne();

        $this->request(['name' => $provider->name])
            ->assertJsonPath('errors.name', __('validation.unique', [
                'attribute' => 'name',
            ]));
    }
}
