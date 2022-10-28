<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Stores;

use App\Groups\StoreTags\StoreTagFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetStoreTagsTest extends TestCase
{
    use RefreshDatabase;

    public function testGetStoreTags(): void
    {
        StoreTagFactory::new()
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->getJson('/stores/tags')
            ->assertOk()
            ->assertExactJson(['bar', 'baz', 'foo']);
    }
}
