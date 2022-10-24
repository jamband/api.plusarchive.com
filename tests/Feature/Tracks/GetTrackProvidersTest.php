<?php

declare(strict_types=1);

namespace Tests\Feature\Tracks;

use App\Groups\MusicProviders\MusicProviderFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetTrackProvidersTest extends TestCase
{
    use RefreshDatabase;

    public function testGetTrackProviders(): void
    {
        MusicProviderFactory::new()
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->getJson('/tracks/providers')
            ->assertOk()
            ->assertExactJson(['bar', 'baz', 'foo']);
    }
}
