<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\MusicProviders;

use App\Groups\MusicProviders\MusicProvider;
use App\Groups\MusicProviders\MusicProviderFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetAdminMusicProvidersTest extends TestCase
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
            ->get('/music-providers/admin')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->get('/music-providers/admin')
            ->assertUnauthorized();
    }

    public function testAdminGetMusicProviders(): void
    {
        /** @var array<int, MusicProvider> $providers */
        $providers = $this->providerFactory
            ->count(2)
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/music-providers/admin')
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJson(function (AssertableJson $json) use ($providers) {
                $json->where('0', [
                    'id' => $providers[1]->id,
                    'name' => $providers[1]->name,
                ]);

                $json->where('1', [
                    'id' => $providers[0]->id,
                    'name' => $providers[0]->name,
                ]);
            });
    }

    public function testAdminGetMusicProvidersWithSortAsc(): void
    {
        /** @var array<int, MusicProvider> $providers */
        $providers = $this->providerFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'name' => 'name'.($sequence->index + 1),
            ]))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/music-providers/admin?sort=name')
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJson(function (AssertableJson $json) use ($providers) {
                $json->has('0', fn (AssertableJson $json) => $json
                    ->where('id', $providers[0]->id)
                    ->etc());
            });
    }

    public function testAdminGetMusicProvidersWithSortDesc(): void
    {
        /** @var array<int, MusicProvider> $providers */
        $providers = $this->providerFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'name' => 'name'.($sequence->index + 1),
            ]))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/music-providers/admin?sort=-name')
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJson(function (AssertableJson $json) use ($providers) {
                $json->has('0', fn (AssertableJson $json) => $json
                    ->where('id', $providers[1]->id)
                    ->etc());
            });
    }

    public function testAdminGetMusicProvidersWithName(): void
    {
        /** @var array<int, MusicProvider> $providers */
        $providers = $this->providerFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/music-providers/admin?name=ba')
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJson(function (AssertableJson $json) use ($providers) {
                $json->has('0', fn (AssertableJson $json) => $json
                    ->where('id', $providers[2]->id)
                    ->etc());
            });
    }

    public function testQueryStringTypes(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->get('/music-providers/admin?name[]=sort[]=')
            ->assertOk();
    }
}
