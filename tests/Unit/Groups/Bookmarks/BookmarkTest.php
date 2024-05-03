<?php

declare(strict_types=1);

namespace Tests\Unit\Groups\Bookmarks;

use App\Groups\Bookmarks\Bookmark;
use App\Groups\Bookmarks\BookmarkFactory;
use App\Groups\Countries\CountryFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookmarkTest extends TestCase
{
    use RefreshDatabase;

    private Bookmark $bookmark;
    private BookmarkFactory $bookmarkFactory;
    private CountryFactory $countryFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookmark = new Bookmark();
        $this->bookmarkFactory = new BookmarkFactory();
        $this->countryFactory = new CountryFactory();
    }

    public function testTimestamps(): void
    {
        $this->assertTrue($this->bookmark->timestamps);
    }

    public function testCountry(): void
    {
        $relation = $this->bookmark->country();
        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame('country_id', $relation->getForeignKeyName());
    }

    public function testTags(): void
    {
        $pivot = $this->bookmark->tags();

        $this->assertInstanceOf(BelongsToMany::class, $pivot);
        $this->assertSame('tag_bookmark', $pivot->getTable());
        $this->assertSame('bookmarks', $pivot->getParent()->getTable());
        $this->assertSame('bookmark_tags', $pivot->getRelated()->getTable());
        $this->assertSame('tag_id', $pivot->getRelatedPivotKeyName());
        $this->assertSame('bookmark_id', $pivot->getForeignPivotKeyName());
    }

    public function testGetCountryNames(): void
    {
        $this->countryFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->bookmarkFactory
            ->count(5)
            ->state(new Sequence(
                ['country_id' => 1],
                ['country_id' => 2],
                ['country_id' => 2],
                ['country_id' => 3],
                ['country_id' => 3],
            ))
            ->create();

        $this->assertSame(['bar', 'baz', 'foo'], $this->bookmark->getCountryNames());
    }
}
