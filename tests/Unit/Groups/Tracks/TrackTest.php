<?php

declare(strict_types=1);

namespace Tests\Unit\Groups\Tracks;

use App\Groups\TrackGenres\TrackGenreFactory;
use App\Groups\Tracks\Track;
use App\Groups\Tracks\TrackFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackTest extends TestCase
{
    use RefreshDatabase;

    private Track $track;
    private TrackFactory $trackFactory;
    private TrackGenreFactory $genreFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->track = new Track();
        $this->trackFactory = new TrackFactory();
        $this->genreFactory = new TrackGenreFactory();
    }

    public function testTimestamps(): void
    {
        $this->assertTrue($this->track->timestamps);
    }

    public function testProvider(): void
    {
        $relation = $this->track->provider();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame('provider_id', $relation->getForeignKeyName());
    }

    public function testGenres(): void
    {
        $pivot = $this->track->genres();

        $this->assertInstanceOf(BelongsToMany::class, $pivot);
        $this->assertSame('genre_track', $pivot->getTable());
        $this->assertSame('tracks', $pivot->getParent()->getTable());
        $this->assertSame('track_genres', $pivot->getRelated()->getTable());
        $this->assertSame('genre_id', $pivot->getRelatedPivotKeyName());
        $this->assertSame('track_id', $pivot->getForeignPivotKeyName());
    }

    public function testGetMinimalGenres(): void
    {
        /** @var array<int, Track> $tracks */
        $tracks = $this->trackFactory
            ->count(5)
            ->create();

        $this->genreFactory
            ->count(5)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'name' => 'genre'.($sequence->index + 1),
            ]))
            ->create();

        $tracks[0]->genres()->sync([1]);
        $tracks[1]->genres()->sync([1]);
        $tracks[2]->genres()->sync([3]);
        $tracks[3]->genres()->sync([3, 5]);
        $tracks[4]->genres()->sync([2, 4, 5]);

        $this->assertSame(
            ['genre1', 'genre3', 'genre5'],
            $this->track->getMinimalGenres(3)
        );
    }

    public function testToggleUrge(): void
    {
        /** @var array<int, Track> $tracks */
        $tracks = $this->trackFactory
            ->count(10)
            ->state(new Sequence(
                ['urge' => false],
                ['urge' => true],
            ))
            ->create();

        $this->assertFalse($tracks[0]->urge);
        $this->assertSame(5, $this->track->where(['urge' => true])->count());

        $this->assertTrue($this->track->toggleUrge($tracks[0]));
        $this->assertSame(6, $this->track->where(['urge' => true])->count());

        $this->assertTrue($this->track->toggleUrge($tracks[0]));
        $this->assertSame(5, $this->track->where(['urge' => true])->count());

        $this->assertTrue($this->track->toggleUrge($tracks[0]));
        $this->assertSame(6, $this->track->where(['urge' => true])->count());

        $this->assertFalse($tracks[2]->urge);
        $this->assertSame(6, $this->track->where(['urge' => true])->count());

        $this->assertFalse($this->track->toggleUrge($tracks[2]));
        $this->assertSame(6, $this->track->where(['urge' => true])->count());
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

        $this->track->stopUrges();

        $this->assertSame(4, $this->track->where('urge', false)->count());
        $this->assertSame(0, $this->track->where('urge', true)->count());
    }
}
