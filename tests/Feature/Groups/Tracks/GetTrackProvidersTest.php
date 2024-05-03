<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Tracks;

use App\Groups\MusicProviders\MusicProviderFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetTrackProvidersTest extends TestCase
{
    use RefreshDatabase;

    private MusicProviderFactory $providerFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->providerFactory = new MusicProviderFactory();
    }

    public function testGetTrackProviders(): void
    {
        $this->providerFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->get('/tracks/providers')
            ->assertOk()
            ->assertExactJson(['bar', 'baz', 'foo']);
    }
}
