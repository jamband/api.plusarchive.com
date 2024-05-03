<?php

declare(strict_types=1);

namespace Tests\Unit\Groups\MusicProviders;

use App\Groups\MusicProviders\MusicProvider;
use App\Groups\MusicProviders\MusicProviderFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MusicProviderTest extends TestCase
{
    use RefreshDatabase;

    private MusicProvider $provider;
    private MusicProviderFactory $providerFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = new MusicProvider();
        $this->providerFactory = new MusicProviderFactory();
    }

    public function testTimestamps(): void
    {
        $this->assertFalse($this->provider->timestamps);
    }

    public function testGetIdByName(): void
    {
        $provider = $this->providerFactory
            ->createOne();

        $this->assertSame(null, $this->provider->getIdByName('foo'));
        $this->assertSame(1, $this->provider->getIdByName($provider->name));
    }

    public function testGetNames(): void
    {
        $this->providerFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->assertSame(['bar', 'baz', 'foo'], $this->provider->getNames());
    }
}
