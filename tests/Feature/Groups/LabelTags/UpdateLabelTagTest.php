<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\LabelTags;

use App\Groups\LabelTags\LabelTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UpdateLabelTagTest extends TestCase
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
            ->put('/label-tags/1')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->put('/label-tags/1')
            ->assertUnauthorized();
    }

    public function testNotFound(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->put('/label-tags/1', [
                'name' => 'foo',
            ])
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testUpdateLabelTag(): void
    {
        $tag = $this->tagFactory
            ->createOne();

        $this->assertDatabaseCount($tag::class, 1);

        $this->actingAs($this->userFactory->makeOne())
            ->put('/label-tags/'.$tag->id, [
                'name' => 'updated_tag1',
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($tag) {
                $json->where('id', $tag->id)
                    ->where('name', 'updated_tag1');
            });

        $this->assertDatabaseCount($tag::class, 1)
            ->assertDatabaseHas($tag::class, [
                'id' => $tag->id,
                'name' => 'updated_tag1',
            ]);
    }
}
