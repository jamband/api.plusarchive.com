<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\MusicProviders;

use App\Groups\MusicProviders\MusicProviderFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UpdateMusicProviderTest extends TestCase
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

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->put('/music-providers/1')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->put('/music-providers/1')
            ->assertUnauthorized();
    }

    public function testModelNotFound(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->put('/music-providers/1', [
                'name' => 'foo',
            ])
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testUpdateMusicProvider(): void
    {
        $provider = $this->providerFactory
            ->createOne();

        $this->assertDatabaseCount($provider::class, 1);

        $this->actingAs($this->userFactory->makeOne())
            ->put('/music-providers/'.$provider->id, [
                'name' => 'updated_name1',
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($provider) {
                $json->where('id', $provider->id)
                    ->where('name', 'updated_name1');
            });

        $this->assertDatabaseCount($provider::class, 1)
            ->assertDatabaseHas($provider::class, [
                'id' => $provider->id,
                'name' => 'updated_name1',
            ]);
    }
}
