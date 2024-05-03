<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Bookmarks;

use App\Groups\Bookmarks\BookmarkFactory;
use App\Groups\Countries\CountryFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetBookmarkCountriesTest extends TestCase
{
    use RefreshDatabase;

    private CountryFactory $countryFactory;
    private BookmarkFactory $bookmarkFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->countryFactory = new CountryFactory();
        $this->bookmarkFactory = new BookmarkFactory();
    }

    public function testGetBookmarkCountries(): void
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

        $this->get('/bookmarks/countries')
            ->assertOk()
            ->assertExactJson(['bar', 'baz', 'foo']);
    }
}
