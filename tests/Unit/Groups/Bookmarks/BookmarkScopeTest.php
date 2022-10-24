<?php

declare(strict_types=1);

namespace Tests\Unit\Groups\Bookmarks;

use App\Groups\Bookmarks\Bookmark;
use App\Groups\Bookmarks\BookmarkFactory;
use App\Groups\BookmarkTags\BookmarkTagFactory;
use App\Groups\Countries\CountryFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookmarkScopeTest extends TestCase
{
    use RefreshDatabase;

    private Bookmark $bookmark;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookmark = new Bookmark();
    }

    public function testScopeOfCountry(): void
    {
        BookmarkFactory::new()
            ->count(1)
            ->for(
                CountryFactory::new()
                    ->state(['name' => 'foo'])
            )
            ->create();

        BookmarkFactory::new()
            ->count(2)
            ->for(
                CountryFactory::new()
                    ->state(['name' => 'bar'])
            )
            ->create();

        $this->assertSame(0, $this->bookmark->ofCountry('')->count());
        $this->assertSame(1, $this->bookmark->ofCountry('foo')->count());
        $this->assertSame(2, $this->bookmark->ofCountry('bar')->count());
        $this->assertSame(0, $this->bookmark->ofCountry('baz')->count());
    }

    public function testScopeOfTag(): void
    {
        /** @var array<int, Bookmark> $bookmarks */
        $bookmarks = BookmarkFactory::new()
            ->count(2)
            ->create();

        BookmarkTagFactory::new()
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $bookmarks[0]->tags()->sync([1, 2]);
        $bookmarks[1]->tags()->sync([2]);

        $this->assertSame(0, $this->bookmark->ofTag('')->count());
        $this->assertSame(1, $this->bookmark->ofTag('foo')->count());
        $this->assertSame(2, $this->bookmark->ofTag('bar')->count());
        $this->assertSame(0, $this->bookmark->ofTag('baz')->count());
    }

    public function testScopeOfSearch(): void
    {
        BookmarkFactory::new()
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->assertSame(0, $this->bookmark->ofSearch('')->count());
        $this->assertSame(1, $this->bookmark->ofSearch('o')->count());
        $this->assertSame(2, $this->bookmark->ofSearch('ba')->count());
        $this->assertSame(0, $this->bookmark->ofSearch('qux')->count());
    }

    public function testScopeInNameOrder(): void
    {
        BookmarkFactory::new()
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->assertSame(
            ['bar', 'baz', 'foo'],
            $this->bookmark->inNameOrder()->pluck('name')->toArray()
        );
    }
}
