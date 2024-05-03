<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Playlists;

use App\Groups\Playlists\PlaylistFactory;
use App\Groups\Users\UserFactory;
use Hashids\Hashids;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeletePlaylistTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private PlaylistFactory $playlistFactory;
    private Hashids $hashids;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->playlistFactory = new PlaylistFactory();
        $this->hashids = $this->app->make(Hashids::class);
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->delete('/playlists/'.str_repeat('a', 11))
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->delete('/playlists/'.str_repeat('a', 11))
            ->assertUnauthorized();
    }

    public function testNotFound(): void
    {
        $this->delete('/playlists/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testModelNotFound(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->delete('/playlists/'.$this->hashids->encode(1))
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testModelNotFoundWithInvalidHashValue(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->delete('/playlists/'.str_repeat('a', 11))
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testDeletePlaylist(): void
    {
        $playlist = $this->playlistFactory
            ->createOne();

        $this->assertDatabaseCount($playlist::class, 1);

        $this->actingAs($this->userFactory->makeOne())
            ->delete('/playlists/'.$this->hashids->encode($playlist->id))
            ->assertNoContent();

        $this->assertDatabaseCount($playlist::class, 0);
    }
}
