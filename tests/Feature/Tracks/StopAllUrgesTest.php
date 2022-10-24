<?php

declare(strict_types=1);

namespace Tests\Feature\Tracks;

use App\Groups\Tracks\Track;
use App\Groups\Tracks\TrackFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestMiddleware;

class StopAllUrgesTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('PATCH /tracks/stop-all-urges');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('PATCH /tracks/stop-all-urges');
    }

    public function testStopAllUrges(): void
    {
        TrackFactory::new()
            ->count(4)
            ->state(new Sequence(
                ['urge' => false],
                ['urge' => true],
            ))
            ->create();

        $this->assertDatabaseHas(Track::class, [
            'urge' => true,
        ]);

        $this->actingAs(UserFactory::new()->makeOne())
            ->patch('/tracks/stop-all-urges')
            ->assertNoContent();

        $this->assertDatabaseMissing(Track::class, [
            'urge' => true,
        ]);
    }
}
