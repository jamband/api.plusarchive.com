<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Labels;

use App\Groups\Labels\Label;
use App\Groups\Labels\LabelFactory;
use App\Groups\Users\UserFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetAdminLabelsTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private LabelFactory $labelFactory;
    private Carbon $carbon;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->labelFactory = new LabelFactory();
        $this->carbon = new Carbon();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->get('/labels/admin')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->get('/labels/admin')
            ->assertUnauthorized();
    }

    public function testGetAdminLabels(): void
    {
        /** @var array<int, Label> $labels */
        $labels = $this->labelFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => ($this->carbon)->addMinutes($sequence->index + 1),
            ]))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/labels/admin')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($labels) {
                $json->where('data.0', [
                    'id' => $labels[1]->id,
                    'name' => $labels[1]->name,
                    'country' => $labels[1]->country->name,
                    'url' => $labels[1]->url,
                    'links' => $labels[1]->links,
                    'tags' => [],
                    'created_at' => $labels[1]->created_at->format('Y-m-d H:i'),
                    'updated_at' => $labels[1]->updated_at->format('Y-m-d H:i'),
                ]);

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetAdminLabelsWithSortAsc(): void
    {
        /** @var array<int, Label> $labels */
        $labels = $this->labelFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'name' => 'name'.($sequence->index),
            ]))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/labels/admin?sort=name')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($labels) {
                $json->has('data.0', fn (AssertableJson $json) => $json
                    ->where('id', $labels[0]->id)
                    ->etc());

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetAdminLabelsWithSortDesc(): void
    {
        /** @var array<int, Label> $labels */
        $labels = $this->labelFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'name' => 'name'.($sequence->index),
            ]))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/labels/admin?sort=-name')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($labels) {
                $json->has('data.0', fn (AssertableJson $json) => $json
                    ->where('id', $labels[1]->id)
                    ->etc());

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetAdminLabelsWithName(): void
    {
        /** @var array<int, Label> $labels */
        $labels = $this->labelFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => ($this->carbon)->addMinutes($sequence->index),
            ]))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/labels/admin?name=ba')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($labels) {
                $json->has('data.0', fn (AssertableJson $json) => $json
                    ->where('id', $labels[2]->id)
                    ->etc());

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testQueryStringTypes(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->get('/labels/admin?name[]=&country[]=&tag[]=&sort[]=')
            ->assertOk();
    }
}
