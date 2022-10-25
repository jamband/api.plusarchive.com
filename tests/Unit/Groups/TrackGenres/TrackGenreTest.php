<?php

declare(strict_types=1);

namespace Tests\Unit\Groups\TrackGenres;

use App\Groups\TrackGenres\TrackGenre;
use App\Groups\TrackGenres\TrackGenreFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackGenreTest extends TestCase
{
    use RefreshDatabase;

    private TrackGenre $genre;

    protected function setUp(): void
    {
        parent::setUp();

        $this->genre = new TrackGenre();
    }

    public function testTimestamps(): void
    {
        $this->assertFalse($this->genre->timestamps);
    }

    public function testGetNames(): void
    {
        TrackGenreFactory::new()
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->assertSame(['bar', 'baz', 'foo'], $this->genre->getNames());
    }

    public function testGetIdsByNames(): void
    {
        $this->assertDatabaseCount($this->genre::class, 0);

        $keys = $this->genre->getIdsByNames(['foo', 'bar', 'baz']);
        $this->assertDatabaseCount($this->genre::class, 3);
        $this->assertSame([1, 2, 3], $keys);

        $keys = $this->genre->getIdsByNames(['bar', 'baz', 'qux']);
        $this->assertDatabaseCount($this->genre::class, 4);
        $this->assertSame([2, 3, 4], $keys);
    }
}
