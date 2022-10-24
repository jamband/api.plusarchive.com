<?php

declare(strict_types=1);

namespace Tests\Unit\Groups\Tracks;

use App\Groups\MusicProviders\MusicProviderFactory;
use App\Groups\TrackGenres\TrackGenreFactory;
use App\Groups\Tracks\Track;
use App\Groups\Tracks\TrackFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackScopeTest extends TestCase
{
    use RefreshDatabase;

    private Track $track;

    protected function setUp(): void
    {
        parent::setUp();

        $this->track = new Track();
    }

    public function testFavorites(): void
    {
        TrackFactory::new()
            ->count(4)
            ->state(new Sequence(
                ['urge' => false],
                ['urge' => true],
            ))
            ->create();

        $this->assertSame(2, $this->track->favorites()->count());
    }

    public function testOfProvider(): void
    {
        TrackFactory::new()
            ->count(1)
            ->for(
                MusicProviderFactory::new()
                    ->state(['name' => 'foo']),
                'provider'
            )
            ->create();

        TrackFactory::new()
            ->count(2)
            ->for(
                MusicProviderFactory::new()
                    ->state(['name' => 'bar']),
                'provider'
            )
            ->create();

        $this->assertSame(0, $this->track->ofProvider('')->count());
        $this->assertSame(1, $this->track->ofProvider('foo')->count());
        $this->assertSame(2, $this->track->ofProvider('bar')->count());
        $this->assertSame(0, $this->track->ofProvider('baz')->count());
    }

    public function testOfUrge(): void
    {
        TrackFactory::new()
            ->count(4)
            ->state(new Sequence(
                ['urge' => false],
                ['urge' => true],
            ))
            ->create();

        $query = $this->track->ofUrge('0');
        $this->assertSame(2, $query->count());

        /** @var array<int, Track> $tracks */
        $tracks = $query->get();
        $this->assertFalse($tracks[0]->urge);
        $this->assertFalse($tracks[1]->urge);

        $query = $this->track->ofUrge('1');
        $this->assertSame(2, $query->count());

        /** @var array<int, Track> $tracks */
        $tracks = $query->get();
        $this->assertTrue($tracks[0]->urge);
        $this->assertTrue($tracks[1]->urge);
    }

    public function testOfGenre(): void
    {
        /** @var array<int, Track> $tracks */
        $tracks = TrackFactory::new()
            ->count(2)
            ->create();

        TrackGenreFactory::new()
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $tracks[0]->genres()->sync([1, 2]);
        $tracks[1]->genres()->sync([2]);

        $this->assertSame(0, $this->track->ofGenre('')->count());
        $this->assertSame(1, $this->track->ofGenre('foo')->count());
        $this->assertSame(2, $this->track->ofGenre('bar')->count());
        $this->assertSame(0, $this->track->ofGenre('baz')->count());
    }

    public function testOfSearch(): void
    {
        TrackFactory::new()
            ->count(3)
            ->state(new Sequence(
                ['title' => 'foo'],
                ['title' => 'bar'],
                ['title' => 'baz'],
            ))
            ->create();

        $this->assertSame(0, $this->track->ofSearch('')->count());
        $this->assertSame(1, $this->track->ofSearch('o')->count());
        $this->assertSame(2, $this->track->ofSearch('ba')->count());
        $this->assertSame(0, $this->track->ofSearch('qux')->count());
    }

    public function testInTitleOrder(): void
    {
        TrackFactory::new()
            ->count(3)
            ->state(new Sequence(
                ['title' => 'foo'],
                ['title' => 'bar'],
                ['title' => 'baz'],
            ))
            ->create();

        $this->assertSame(
            ['bar', 'baz', 'foo'],
            $this->track->inTitleOrder()->pluck('title')->toArray()
        );
    }
}
