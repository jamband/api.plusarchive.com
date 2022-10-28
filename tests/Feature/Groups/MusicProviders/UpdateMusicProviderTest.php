<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\MusicProviders;

use App\Groups\MusicProviders\MusicProvider;
use App\Groups\MusicProviders\MusicProviderFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class UpdateMusicProviderTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('PUT /music-providers/1');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('PUT /music-providers/1');
    }

    public function testModelNotFound(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/music-providers/1', [
                'name' => 'foo',
            ])
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testUpdateMusicProvider(): void
    {
        $provider = MusicProviderFactory::new()
            ->createOne();

        $this->assertDatabaseCount(MusicProvider::class, 1);

        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/music-providers/'.$provider->id, [
                'name' => 'updated_name1',
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($provider) {
                $json->where('id', $provider->id)
                    ->where('name', 'updated_name1');
            });

        $this->assertDatabaseCount(MusicProvider::class, 1);

        $this->assertDatabaseHas(MusicProvider::class, [
            'id' => $provider->id,
            'name' => 'updated_name1',
        ]);
    }
}
