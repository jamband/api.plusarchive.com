<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Playlists;

use App\Groups\Playlists\Playlist;
use App\Groups\Playlists\PlaylistFactory;
use App\Groups\Users\UserFactory;
use Hashids\Hashids;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestMiddleware;

class DeletePlaylistTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    private Hashids $hashids;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hashids = $this->app->make(Hashids::class);
    }

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware(
            'DELETE /playlists/'.str_repeat('a', 11)
        );
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware(
            'DELETE /playlists/'.str_repeat('a', 11)
        );
    }

    public function testNotFound(): void
    {
        $this->deleteJson('/playlists/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testModelNotFound(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/playlists/'.$this->hashids->encode(1))
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testModelNotFoundWithInvalidHashValue(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/playlists/'.str_repeat('a', 11))
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testDeletePlaylist(): void
    {
        $playlist = PlaylistFactory::new()
            ->createOne();

        $this->assertDatabaseCount(Playlist::class, 1);

        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/playlists/'.$this->hashids->encode($playlist->id))
            ->assertNoContent();

        $this->assertDatabaseCount(Playlist::class, 0);
    }
}
