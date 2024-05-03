<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\MusicProviders;

use App\Groups\MusicProviders\MusicProvider;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CreateMusicProviderTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private MusicProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->provider = new MusicProvider();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->post('/music-providers')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->post('/music-providers')
            ->assertUnauthorized();
    }

    public function testCreateMusicProvider(): void
    {
        $this->assertDatabaseCount($this->provider::class, 0);

        $this->actingAs($this->userFactory->makeOne())
            ->post('/music-providers', [
                'name' => 'provider1',
            ])
            ->assertCreated()
            ->assertHeader(
                'Location',
                $this->app['config']['app.url'].'/music-providers/1'
            )
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('id', 1)
                ->where('name', 'provider1'));

        $this->assertDatabaseCount($this->provider::class, 1)
            ->assertDatabaseHas($this->provider::class, [
                'id' => 1,
                'name' => 'provider1',
            ]);
    }
}
