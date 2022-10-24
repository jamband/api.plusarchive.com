<?php

declare(strict_types=1);

namespace Tests\Unit\Groups\LabelTags;

use App\Groups\LabelTags\LabelTag;
use App\Groups\LabelTags\LabelTagFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LabelTagScopeTest extends TestCase
{
    use RefreshDatabase;

    private LabelTag $tag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tag = new LabelTag();
    }

    public function testScopeOfName(): void
    {
        LabelTagFactory::new()
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
