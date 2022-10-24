<?php

declare(strict_types=1);

namespace Tests\Unit\Groups\BookmarkTags;

use App\Groups\BookmarkTags\BookmarkTag;
use App\Groups\BookmarkTags\BookmarkTagFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookmarkTagScopeTest extends TestCase
{
    use RefreshDatabase;

    private BookmarkTag $tag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tag = new BookmarkTag();
    }

    public function testScopeOfName(): void
    {
        BookmarkTagFactory::new()
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
