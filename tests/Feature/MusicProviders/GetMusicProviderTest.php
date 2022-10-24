<?php

declare(strict_types=1);

namespace Tests\Feature\MusicProviders;

use App\Groups\MusicProviders\MusicProviderFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class GetMusicProviderTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('GET /music-providers/1');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('GET /music-providers/1');
    }

    public function testModelNotFound(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/music-providers/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testGetMusicProviders(): void
    {
        $provider = MusicProviderFactory::new()
            ->createOne();

        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/music-providers/'.$provider->id)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($provider) {
                $json->where('id', $provider->id)
                    ->where('name', $provider->name);
            });
    }
}
