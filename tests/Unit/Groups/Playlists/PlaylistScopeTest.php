<?php

declare(strict_types=1);

namespace Tests\Unit\Groups\Playlists;

use App\Groups\MusicProviders\MusicProviderFactory;
use App\Groups\Playlists\Playlist;
use App\Groups\Playlists\PlaylistFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaylistScopeTest extends TestCase
{
    use RefreshDatabase;

    private Playlist $playlist;
    private PlaylistFactory $playlistFactory;
    private MusicProviderFactory $providerFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->playlist = new Playlist();
        $this->playlistFactory = new PlaylistFactory();
        $this->providerFactory = new MusicProviderFactory();
    }

    public function testScopeOfProvider(): void
    {
        $this->playlistFactory
            ->count(1)
            ->for(
                $this->providerFactory
                    ->state(['name' => 'foo']),
                'provider'
            )
            ->create();

        $this->playlistFactory
            ->count(2)
            ->for(
                $this->providerFactory
                    ->state(['name' => 'bar']),
                'provider'
            )
            ->create();

        $this->assertSame(0, $this->playlist->ofProvider('')->count());
        $this->assertSame(1, $this->playlist->ofProvider('foo')->count());
        $this->assertSame(2, $this->playlist->ofProvider('bar')->count());
        $this->assertSame(0, $this->playlist->ofProvider('baz')->count());
    }

    public function testScopeOfSearch(): void
    {
        $this->playlistFactory
            ->count(3)
            ->state(new Sequence(
                ['title' => 'foo'],
                ['title' => 'bar'],
                ['title' => 'baz'],
            ))
            ->create();

        $this->assertSame(0, $this->playlist->ofSearch('')->count());
        $this->assertSame(1, $this->playlist->ofSearch('o')->count());
        $this->assertSame(2, $this->playlist->ofSearch('ba')->count());
        $this->assertSame(0, $this->playlist->ofSearch('qux')->count());
    }
}
