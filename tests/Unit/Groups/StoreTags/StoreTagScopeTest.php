<?php

declare(strict_types=1);

namespace Tests\Unit\Groups\StoreTags;

use App\Groups\StoreTags\StoreTag;
use App\Groups\StoreTags\StoreTagFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTagScopeTest extends TestCase
{
    use RefreshDatabase;

    private StoreTag $tag;
    private StoreTagFactory $tagFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tag = new StoreTag();
        $this->tagFactory = new StoreTagFactory();
    }

    public function testScopeOfName(): void
    {
        $this->tagFactory
            ->count(2)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
            ))
            ->create();

        $this->assertSame(0, $this->tag->ofName('')->count());
        $this->assertSame(1, $this->tag->ofName('foo')->count());
        $this->assertSame(1, $this->tag->ofName('bar')->count());
        $this->assertSame(0, $this->tag->ofName('baz')->count());
    }
}
