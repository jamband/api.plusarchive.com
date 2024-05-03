<?php

declare(strict_types=1);

namespace Tests\Unit\Groups\TrackGenres;

use App\Groups\TrackGenres\TrackGenre;
use App\Groups\TrackGenres\TrackGenreFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackGenreScopeTest extends TestCase
{
    use RefreshDatabase;

    private TrackGenre $genre;
    private TrackGenreFactory $genreFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->genre = new TrackGenre();
        $this->genreFactory = new TrackGenreFactory();
    }

    public function testScopeOfName(): void
    {
        $this->genreFactory
            ->count(2)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
            ))
            ->create();

        $this->assertSame(0, $this->genre->ofName('')->count());
        $this->assertSame(1, $this->genre->ofName('foo')->count());
        $this->assertSame(1, $this->genre->ofName('bar')->count());
        $this->assertSame(0, $this->genre->ofName('baz')->count());
    }
}
