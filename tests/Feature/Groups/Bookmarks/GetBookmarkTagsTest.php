<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Bookmarks;

use App\Groups\BookmarkTags\BookmarkTagFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetBookmarkTagsTest extends TestCase
{
    use RefreshDatabase;

    private BookmarkTagFactory $tagFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tagFactory = new BookmarkTagFactory();
    }

    public function testGetBookmarkTags(): void
    {
        $this->tagFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->get('/bookmarks/tags')
            ->assertOk()
            ->assertExactJson(['bar', 'baz', 'foo']);
    }
}
