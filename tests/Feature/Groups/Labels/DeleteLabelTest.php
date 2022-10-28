<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Labels;

use App\Groups\Labels\Label;
use App\Groups\Labels\LabelFactory;
use App\Groups\LabelTags\LabelTag;
use App\Groups\LabelTags\LabelTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestMiddleware;

class DeleteLabelTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('DELETE /labels/1');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('DELETE /labels/1');
    }

    public function testModelNotFound(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/labels/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testDeleteLabel(): void
    {
        $label = LabelFactory::new()
            ->createOne();

        $this->assertDatabaseCount(Label::class, 1);

        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/labels/'.$label->id)
            ->assertNoContent();

        $this->assertDatabaseCount(Label::class, 0);
    }

    public function testDeleteLabelWithTags(): void
    {
        $label = LabelFactory::new()
            ->createOne();

        $pivotTable = $label->tags()->getTable();

        LabelTagFactory::new()
            ->count(2)
            ->create();

        $label->tags()->sync([1, 2]);

        $this->assertDatabaseCount(Label::class, 1);
        $this->assertDatabaseCount(LabelTag::class, 2);
        $this->assertDatabaseCount($pivotTable, 2);

        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/labels/'.$label->id)
            ->assertNoContent();

        $this->assertDatabaseCount(Label::class, 0);
        $this->assertDatabaseCount(LabelTag::class, 2);
        $this->assertDatabaseCount($pivotTable, 0);
    }
}
