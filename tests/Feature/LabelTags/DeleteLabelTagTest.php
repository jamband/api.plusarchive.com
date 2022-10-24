<?php

declare(strict_types=1);

namespace Tests\Feature\LabelTags;

use App\Groups\LabelTags\LabelTag;
use App\Groups\LabelTags\LabelTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestMiddleware;

class DeleteLabelTagTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('DELETE /label-tags/1');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('DELETE /label-tags/1');
    }

    public function testModelNotFound(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/label-tags/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testDeleteLabelTag(): void
    {
        $tag = LabelTagFactory::new()
            ->createOne();

        $this->assertDatabaseCount(LabelTag::class, 1);

        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/label-tags/'.$tag->id)
            ->assertNoContent();

        $this->assertDatabaseCount(LabelTag::class, 0);
    }
}
