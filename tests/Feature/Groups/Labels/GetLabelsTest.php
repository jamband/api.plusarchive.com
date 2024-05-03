<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Labels;

use App\Groups\Countries\CountryFactory;
use App\Groups\Labels\Label;
use App\Groups\Labels\LabelFactory;
use App\Groups\LabelTags\LabelTagFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetLabelsTest extends TestCase
{
    use RefreshDatabase;

    private LabelFactory $labelFactory;
    private LabelTagFactory $tagFactory;
    private CountryFactory $countryFactory;
    private Carbon $carbon;

    protected function setUp(): void
    {
        parent::setUp();

        $this->labelFactory = new LabelFactory();
        $this->tagFactory = new LabelTagFactory();
        $this->countryFactory = new CountryFactory();
        $this->carbon = new Carbon();
    }

    public function testGetLabels(): void
    {
        /** @var array<int, Label> $labels */
        $labels = $this->labelFactory
            ->count(2)
            ->hasAttached(
                factory: $this->tagFactory
                    ->count(2),
                relationship: 'tags',
            )
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => ($this->carbon::now())->addMinutes($sequence->index),
            ]))
            ->create();

        $this->get('/labels')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($labels) {
                $json->where('data.0', [
                    'name' => $labels[1]->name,
                    'url' => $labels[1]->url,
                    'links' => $labels[1]->links,
                    'country' => $labels[1]->country->name,
                    'tags' => [
                        $labels[1]->tags[0]->name,
                        $labels[1]->tags[1]->name,
                    ],
                ]);

                $json->where('data.1', [
                    'name' => $labels[0]->name,
                    'url' => $labels[0]->url,
                    'links' => $labels[0]->links,
                    'country' => $labels[0]->country->name,
                    'tags' => [
                        $labels[0]->tags[0]->name,
                        $labels[0]->tags[1]->name,
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

    public function testGetLabelsWithCountry(): void
    {
        $this->labelFactory
            ->for(
                $this->countryFactory
                    ->state(['name' => 'foo']),
            )
            ->createOne();

        /** @var array<int, Label> $labels */
        $labels = $this->labelFactory
            ->count(2)
            ->for(
                $this->countryFactory
                    ->state(['name' => 'bar']),
            )
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => ($this->carbon::now())->addMinutes($sequence->index),
            ]))
            ->create();

        $this->get('/labels?country=bar')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($labels) {
                $json->where('data.0', [
                    'name' => $labels[1]->name,
                    'url' => $labels[1]->url,
                    'links' => $labels[1]->links,
                    'country' => 'bar',
                    'tags' => [],
                ])
                ->where('data.1', [
                    'name' => $labels[0]->name,
                    'url' => $labels[0]->url,
                    'links' => $labels[0]->links,
                    'country' => 'bar',
                    'tags' => [],
                ])
                ->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetLabelsWithUnmatchedCountry(): void
    {
        $this->labelFactory
            ->for(
                $this->countryFactory
                    ->state(['name' => 'foo']),
            )
            ->createOne();

        $this->get('/labels?country=bar')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data', [])
                ->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 0)
                    ->etc()));
    }

    public function testGetLabelsWithTag(): void
    {
        $this->labelFactory
            ->hasAttached(
                factory: $this->tagFactory
                    ->state(['name' => 'foo']),
                relationship: 'tags',
            )
            ->createOne();

        /** @var array<int, Label> $labels */
        $labels = $this->labelFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => ($this->carbon::now())->addMinutes($sequence->index),
            ]))
            ->create();

        $this->tagFactory
            ->state(['name' => 'bar'])
            ->createOne();

        $labels[0]->tags()->sync([2]);
        $labels[1]->tags()->sync([2]);

        $this->get('/labels?tag=bar')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($labels) {
                $json->where('data.0', [
                    'name' => $labels[1]->name,
                    'url' => $labels[1]->url,
                    'links' => $labels[1]->links,
                    'country' => $labels[1]->country->name,
                    'tags' => ['bar'],
                ]);

                $json->where('data.1', [
                    'name' => $labels[0]->name,
                    'url' => $labels[0]->url,
                    'links' => $labels[0]->links,
                    'country' => $labels[0]->country->name,
                    'tags' => ['bar'],
                ]);

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetLabelsWithUnmatchedTag(): void
    {
        $this->labelFactory
            ->hasAttached(
                factory: $this->tagFactory
                    ->state(['name' => 'foo']),
                relationship: 'tags',
            )
            ->createOne();

        $this->get('/labels?tag=bar')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data', [])
                ->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 0)
                    ->etc()));
    }

    public function testGetLabelsWithCountryAndTag(): void
    {
        $this->countryFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'name' => 'country'.($sequence->index + 1),
            ]))
            ->create();

        /** @var array<int, Label> $labels */
        $labels = $this->labelFactory
            ->count(4)
            ->state(new Sequence(
                ['country_id' => 1],
                ['country_id' => 1],
                ['country_id' => 1],
                ['country_id' => 2],
            ))
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => ($this->carbon::now())->addMinutes($sequence->index),
            ]))
            ->create();

        $this->tagFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'name' => 'tag'.($sequence->index + 1),
            ]))
            ->create();

        $labels[0]->tags()->sync([1]);
        $labels[1]->tags()->sync([1]);

        $this->get('/labels?country=country1&tag=tag1')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($labels) {
                $json->where('data.0', [
                    'name' => $labels[1]->name,
                    'url' => $labels[1]->url,
                    'links' => $labels[1]->links,
                    'country' => 'country1',
                    'tags' => ['tag1'],
                ]);

                $json->where('data.1', [
                    'name' => $labels[0]->name,
                    'url' => $labels[0]->url,
                    'links' => $labels[0]->links,
                    'country' => 'country1',
                    'tags' => ['tag1'],
                ]);

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testQueryStringTypes(): void
    {
        $this->get('/labels?country[]=&tag[]=')
            ->assertOk();
    }
}
