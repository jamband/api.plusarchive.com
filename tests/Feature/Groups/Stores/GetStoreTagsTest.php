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

    private StoreTagFactory $tagFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tagFactory = new StoreTagFactory();
    }

    public function testGetStoreTags(): void
    {
        $this->tagFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->get('/stores/tags')
            ->assertOk()
            ->assertExactJson(['bar', 'baz', 'foo']);
    }
}
