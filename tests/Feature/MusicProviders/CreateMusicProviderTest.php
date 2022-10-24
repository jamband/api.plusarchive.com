<?php

declare(strict_types=1);

namespace Tests\Feature\MusicProviders;

use App\Groups\MusicProviders\MusicProvider;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class CreateMusicProviderTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('POST /music-providers');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('POST /music-providers');
    }

    public function testCreateMusicProvider(): void
    {
        $this->assertDatabaseCount(MusicProvider::class, 0);

        $this->actingAs(UserFactory::new()->makeOne())
            ->postJson('/music-providers', [
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

        $this->assertDatabaseCount(MusicProvider::class, 1);

        $this->assertDatabaseHas(MusicProvider::class, [
            'id' => 1,
            'name' => 'provider1',
        ]);
    }
}
