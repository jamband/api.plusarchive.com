<?php

declare(strict_types=1);

namespace Tests\Feature\Labels;

use App\Groups\Labels\Label;
use App\Groups\Labels\LabelFactory;
use App\Groups\Users\UserFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class GetAdminLabelsTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('GET /labels/admin');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('GET /labels/admin');
    }

    public function testGetAdminLabels(): void
    {
        /** @var array<int, Label> $labels */
        $labels = LabelFactory::new()
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => (new Carbon())->addMinutes($sequence->index + 1),
            ]))
            ->create();

        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/labels/admin')
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
        $labels = LabelFactory::new()
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'name' => 'name'.($sequence->index),
            ]))
            ->create();

        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/labels/admin?sort=name')
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
        $labels = LabelFactory::new()
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'name' => 'name'.($sequence->index),
            ]))
            ->create();

        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/labels/admin?sort=-name')
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
        $labels = LabelFactory::new()
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => (new Carbon())->addMinutes($sequence->index),
            ]))
            ->create();

        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/labels/admin?name=ba')
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
        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/labels/admin?name[]=&country[]=&tag[]=&sort[]=')
            ->assertOk();
    }
}
