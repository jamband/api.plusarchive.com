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

    public function testGetLabelTags(): void
    {
        LabelTagFactory::new()
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->getJson('/labels/tags')
            ->assertOk()
            ->assertExactJson(['bar', 'baz', 'foo']);
    }
}
