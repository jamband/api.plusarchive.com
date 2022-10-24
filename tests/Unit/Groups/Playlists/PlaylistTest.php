<?php

declare(strict_types=1);

namespace Tests\Unit\Groups\Playlists;

use App\Groups\Playlists\Playlist;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaylistTest extends TestCase
{
    use RefreshDatabase;

    private Playlist $playlist;

    protected function setup(): void
    {
        parent::setUp();

        $this->playlist = new Playlist();
    }

    public function testTimestamps(): void
    {
        $this->assertTrue($this->playlist->timestamps);
    }

    public function testProvider(): void
    {
        $relation = $this->playlist->provider();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame('provider_id', $relation->getForeignKeyName());
    }
}
