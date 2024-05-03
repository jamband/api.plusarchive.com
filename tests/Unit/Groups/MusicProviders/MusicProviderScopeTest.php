<?php

declare(strict_types=1);

namespace Tests\Unit\Groups\MusicProviders;

use App\Groups\MusicProviders\MusicProvider;
use App\Groups\MusicProviders\MusicProviderFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MusicProviderScopeTest extends TestCase
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

    public function testScopeSearch(): void
    {
        $this->providerFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->assertSame(0, $this->provider->ofSearch('')->count());
        $this->assertSame(1, $this->provider->ofSearch('o')->count());
        $this->assertSame(2, $this->provider->ofSearch('ba')->count());
        $this->assertSame(0, $this->provider->ofSearch('qux')->count());
    }

    public function testScopeInNameOrder(): void
    {
        $this->providerFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        /** @var array<int, MusicProvider> $providers */
        $providers = $this->provider->inNameOrder()->get();

        $this->assertSame('bar', $providers[0]->name);
        $this->assertSame('baz', $providers[1]->name);
        $this->assertSame('foo', $providers[2]->name);
    }
}
