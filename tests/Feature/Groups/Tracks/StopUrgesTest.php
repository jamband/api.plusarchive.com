<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Tracks;

use App\Groups\Tracks\Track;
use App\Groups\Tracks\TrackFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StopUrgesTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private TrackFactory $trackFactory;
    private Track $track;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->trackFactory = new TrackFactory();
        $this->track = new Track();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->patch('/tracks/stop-urges')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->patch('/tracks/stop-urges')
            ->assertUnauthorized();
    }

    public function testStopUrges(): void
    {
        $this->trackFactory
            ->count(4)
            ->state(new Sequence(
                ['urge' => false],
                ['urge' => true],
            ))
            ->create();

        $this->assertDatabaseHas($this->track::class, [
            'urge' => true,
        ]);

        $this->actingAs($this->userFactory->makeOne())
            ->patch('/tracks/stop-urges')
            ->assertNoContent();

        $this->assertDatabaseMissing($this->track::class, [
            'urge' => true,
        ]);
    }
}
