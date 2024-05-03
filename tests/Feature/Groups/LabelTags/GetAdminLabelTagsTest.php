<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\LabelTags;

use App\Groups\LabelTags\LabelTag;
use App\Groups\LabelTags\LabelTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetAdminLabelTagsTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private LabelTagFactory $tagFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->tagFactory = new LabelTagFactory();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->get('/label-tags/admin')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->get('/label-tags/admin')
            ->assertUnauthorized();
    }

    public function testGetAdminLabelTags(): void
    {
        /** @var array<int, LabelTag> $tags */
        $tags = $this->tagFactory
            ->count(3)
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/label-tags/admin')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJson(function (AssertableJson $json) use ($tags) {
                $json->where('data.0', [
                    'id' => $tags[2]->id,
                    'name' => $tags[2]->name,
                ]);

                $json->where('data.1', [
                    'id' => $tags[1]->id,
                    'name' => $tags[1]->name,
                ]);

                $json->where('data.2', [
                    'id' => $tags[0]->id,
                    'name' => $tags[0]->name,
                ]);

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 3)
                    ->etc());
            });
    }

    public function testGetAdminLabelTagsWithSortAsc(): void
    {
        /** @var array<int, LabelTag> $tags */
        $tags = $this->tagFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'name' => 'name'.($sequence->index),
            ]))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/label-tags/admin?sort=name')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($tags) {
                $json->has('data.0', fn (AssertableJson $json) => $json
                    ->where('id', $tags[0]->id)
                    ->etc());

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetAdminLabelTagsWithSortDesc(): void
    {
        /** @var array<int, LabelTag> $tags */
        $tags = $this->tagFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'name' => 'name'.($sequence->index),
            ]))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/label-tags/admin?sort=-name')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($tags) {
                $json->has('data.0', fn (AssertableJson $json) => $json
                    ->where('id', $tags[1]->id)
                    ->etc());

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetAdminLabelTagsWithName(): void
    {
        /** @var array<int, LabelTag> $tags */
        $tags = $this->tagFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/label-tags/admin?name=ba')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($tags) {
                $json->has('data.0', fn (AssertableJson $json) => $json
                    ->where('id', $tags[2]->id)
                    ->etc());

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testQueryStringTypes(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->get('/label-tags/admin?name[]=&sort[]=')
            ->assertOk();
    }
}
