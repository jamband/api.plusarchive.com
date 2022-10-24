<?php

declare(strict_types=1);

namespace Tests\Feature\Bookmarks;

use App\Groups\Bookmarks\BookmarkFactory;
use App\Groups\Countries\CountryFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetBookmarkCountriesTest extends TestCase
{
    use RefreshDatabase;

    public function testGetBookmarkCountries(): void
    {
        CountryFactory::new()
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        BookmarkFactory::new()
            ->count(5)
            ->state(new Sequence(
                ['country_id' => 1],
                ['country_id' => 2],
                ['country_id' => 2],
                ['country_id' => 3],
                ['country_id' => 3],
            ))
            ->create();

        $this->getJson('/bookmarks/countries')
            ->assertOk()
            ->assertExactJson(['bar', 'baz', 'foo']);
    }
}
