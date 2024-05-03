<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Labels;

use App\Groups\LabelTags\LabelTagFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetLabelTagsTest extends TestCase
{
    use RefreshDatabase;

    private LabelTagFactory $tagFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tagFactory = new LabelTagFactory();
    }

    public function testGetLabelTags(): void
    {
        $this->tagFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->get('/labels/tags')
            ->assertOk()
            ->assertExactJson(['bar', 'baz', 'foo']);
    }
}
