<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\MusicProviders;

use App\Groups\MusicProviders\MusicProviderFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetMusicProviderTest extends TestCase
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
            ->get('/music-providers/1')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->get('/music-providers/1')
            ->assertUnauthorized();
    }

    public function testNotFound(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->get('/music-providers/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testGetMusicProviders(): void
    {
        $provider = $this->providerFactory
            ->createOne();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/music-providers/'.$provider->id)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($provider) {
                $json->where('id', $provider->id)
                    ->where('name', $provider->name);
            });
    }
}
