<?php

declare(strict_types=1);

namespace Tests\Unit\Groups\StoreTags;

use App\Groups\StoreTags\StoreTag;
use App\Groups\StoreTags\StoreTagFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTagTest extends TestCase
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

    public function testTimestamp(): void
    {
        $this->assertFalse($this->tag->timestamps);
    }

    public function testGetNames(): void
    {
        $this->tagFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->assertSame(['bar', 'baz', 'foo'], $this->tag->getNames());
    }

    public function testGetIdsByNames(): void
    {
        $this->assertDatabaseCount($this->tag::class, 0);

        $keys = $this->tag->getIdsByNames(['foo', 'bar', 'baz']);
        $this->assertDatabaseCount($this->tag::class, 3);
        $this->assertSame([1, 2, 3], $keys);

        $keys = $this->tag->getIdsByNames(['bar', 'baz', 'qux']);
        $this->assertDatabaseCount($this->tag::class, 4);
        $this->assertSame([2, 3, 4], $keys);
    }
}
