<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Tracks;

use App\Groups\Tracks\Track;
use App\Groups\Tracks\TrackFactory;
use App\Groups\Users\UserFactory;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestMiddleware;

class ToggleUrgeTest extends TestCase
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
        $this->assertVerifiedMiddleware('PATCH /tracks/'.str_repeat('a', 11).'/toggle-urge');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('PATCH /tracks/'.str_repeat('a', 11).'/toggle-urge');
    }

    public function testNotFound(): void
    {
        $this->patchJson('/tracks/1/toggle-urge')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testModelNotFound(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->patchJson('/tracks/'.$this->hashids->encode(1).'/toggle-urge')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testBadRequest(): void
    {
        /** @var array<int, Track> $tracks */
        $tracks = TrackFactory::new()
            ->count(12)
            ->state(new Sequence(
                ['urge' => false],
                ['urge' => true],
            ))
            ->create();

        $this->actingAs(UserFactory::new()->makeOne())
            ->patchJson('/tracks/'.$this->hashids->encode($tracks[0]->id).'/toggle-urge')
            ->assertStatus(400)
            ->assertExactJson(['message' => 'Can\'t urge more.']);
    }

    public function testToggleUrge(): void
    {
        $track = TrackFactory::new()
            ->createOne();

        $this->assertDatabaseHas(Track::class, [
            'urge' => false,
        ]);

        $this->actingAs(UserFactory::new()->makeOne())
            ->patchJson('/tracks/'.$this->hashids->encode($track->id).'/toggle-urge')
            ->assertNoContent();

        $this->assertDatabaseHas(Track::class, [
            'id' => $track->id,
            'urge' => true,
        ]);
    }
}
