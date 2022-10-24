<?php

declare(strict_types=1);

namespace Tests\Feature\MusicProviders;

use App\Groups\MusicProviders\MusicProvider;
use App\Groups\MusicProviders\MusicProviderFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestMiddleware;

class DeleteMusicProviderTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('DELETE /music-providers/1');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('DELETE /music-providers/1');
    }

    public function testModelNotFound(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/music-providers/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testDeleteMusicProvider(): void
    {
        $provider = MusicProviderFactory::new()
            ->createOne();

        $this->assertDatabaseCount(MusicProvider::class, 1);

        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/music-providers/'.$provider->id)
            ->assertNoContent();

        $this->assertDatabaseCount(MusicProvider::class, 0);
    }
}
