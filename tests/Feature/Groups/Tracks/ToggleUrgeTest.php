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

class ToggleUrgeTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private TrackFactory $trackFactory;
    private Hashids $hashids;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->trackFactory = new TrackFactory();
        $this->hashids = $this->app->make(Hashids::class);
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->patch('/tracks/'.str_repeat('a', 11).'/toggle-urge')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->patch('/tracks/'.str_repeat('a', 11).'/toggle-urge')
            ->assertUnauthorized();
    }

    public function testNotFound(): void
    {
        $this->patch('/tracks/1/toggle-urge')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);

        $this->actingAs($this->userFactory->makeOne())
            ->patch('/tracks/'.$this->hashids->encode(1).'/toggle-urge')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testBadRequest(): void
    {
        /** @var array<int, Track> $tracks */
        $tracks = $this->trackFactory
            ->count(12)
            ->state(new Sequence(
                ['urge' => false],
                ['urge' => true],
            ))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->patch('/tracks/'.$this->hashids->encode($tracks[0]->id).'/toggle-urge')
            ->assertBadRequest()
            ->assertExactJson(['message' => 'Can\'t urge more.']);
    }

    public function testToggleUrge(): void
    {
        $track = $this->trackFactory
            ->createOne();

        $this->assertDatabaseHas($track::class, [
            'urge' => false,
        ]);

        $this->actingAs($this->userFactory->makeOne())
            ->patch('/tracks/'.$this->hashids->encode($track->id).'/toggle-urge')
            ->assertNoContent();

        $this->assertDatabaseHas($track::class, [
            'id' => $track->id,
            'urge' => true,
        ]);
    }
}
