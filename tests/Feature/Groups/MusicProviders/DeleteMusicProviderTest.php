<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\MusicProviders;

use App\Groups\MusicProviders\MusicProviderFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteMusicProviderTest extends TestCase
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
            ->delete('/music-providers/1')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->delete('/music-providers/1')
            ->assertUnauthorized();
    }

    public function testNotFound(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->delete('/music-providers/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testDeleteMusicProvider(): void
    {
        $provider = $this->providerFactory
            ->createOne();

        $this->assertDatabaseCount($provider::class, 1);

        $this->actingAs($this->userFactory->makeOne())
            ->delete('/music-providers/'.$provider->id)
            ->assertNoContent();

        $this->assertDatabaseCount($provider::class, 0);
    }
}
