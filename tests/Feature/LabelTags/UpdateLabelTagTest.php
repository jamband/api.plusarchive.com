<?php

declare(strict_types=1);

namespace Tests\Feature\LabelTags;

use App\Groups\LabelTags\LabelTag;
use App\Groups\LabelTags\LabelTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class UpdateLabelTagTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('PUT /label-tags/1');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('PUT /label-tags/1');
    }

    public function testModelNotFound(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/label-tags/1', [
                'name' => 'foo',
            ])
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testUpdateLabelTag(): void
    {
        $tag = LabelTagFactory::new()
            ->createOne();

        $this->assertDatabaseCount(LabelTag::class, 1);

        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/label-tags/'.$tag->id, [
                'name' => 'updated_tag1',
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($tag) {
                $json->where('id', $tag->id)
                    ->where('name', 'updated_tag1');
            });

        $this->assertDatabaseCount(LabelTag::class, 1);

        $this->assertDatabaseHas(LabelTag::class, [
            'id' => $tag->id,
            'name' => 'updated_tag1',
        ]);
    }
}
