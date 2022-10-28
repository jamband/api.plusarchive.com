<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Labels;

use App\Groups\Labels\Label;
use App\Groups\Labels\LabelFactory;
use App\Groups\LabelTags\LabelTagFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetSearchLabelsTest extends TestCase
{
    use RefreshDatabase;

    public function testGetSearchLabels(): void
    {
        /** @var array<int, Label> $labels */
        $labels = LabelFactory::new()
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->hasAttached(
                factory: LabelTagFactory::new()
                    ->count(2),
                relationship: 'tags',
            )
            ->create();

        $this->getJson('/labels/search?q=ba')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($labels) {
                $json->where('data.0', [
                    'name' => $labels[1]->name,
                    'country' => $labels[1]->country->name,
                    'url' => $labels[1]->url,
                    'links' => $labels[1]->links,
                    'tags' => [
                        $labels[1]->tags[0]->name,
                        $labels[1]->tags[1]->name,
                    ],
                ]);

                $json->where('data.1', [
                    'name' => $labels[2]->name,
                    'country' => $labels[2]->country->name,
                    'url' => $labels[2]->url,
                    'links' => $labels[2]->links,
                    'tags' => [
                        $labels[2]->tags[0]->name,
                        $labels[2]->tags[1]->name,
                    ],
                ]);

                $json->where('pagination', [
                    'currentPage' => 1,
                    'lastPage' => 1,
                    'perPage' => 14,
                    'total' => 2,
                ]);
            });
    }

    public function testGetSearchLabelsWithoutParameter(): void
    {
        LabelFactory::new()
            ->createOne();

        $this->getJson('/labels/search')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data', [])
                ->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 0)
                    ->etc()));
    }

    public function testGetSearchLabelsWithUnmatchedSearch(): void
    {
        LabelFactory::new()
            ->state(['name' => 'foo'])
            ->createOne();

        $this->getJson('/labels/search?q=bar')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data', [])
                ->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 0)
                    ->etc()));
    }

    public function testQueryStringTypes(): void
    {
        $this->getJson('/labels/search?q[]=')
            ->assertOk();
    }
}
