<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Labels;

use App\Groups\Labels\LabelFactory;
use App\Groups\LabelTags\LabelTag;
use App\Groups\LabelTags\LabelTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteLabelTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private LabelFactory $labelFactory;
    private LabelTagFactory $tagFactory;
    private LabelTag $tag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->labelFactory = new LabelFactory();
        $this->tagFactory = new LabelTagFactory();
        $this->tag = new LabelTag();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->delete('/labels/1')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->delete('/labels/1')
            ->assertUnauthorized();
    }

    public function testModelNotFound(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->delete('/labels/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testDeleteLabel(): void
    {
        $label = $this->labelFactory
            ->createOne();

        $this->assertDatabaseCount($label::class, 1);

        $this->actingAs($this->userFactory->makeOne())
            ->delete('/labels/'.$label->id)
            ->assertNoContent();

        $this->assertDatabaseCount($label::class, 0);
    }

    public function testDeleteLabelWithTags(): void
    {
        $label = $this->labelFactory
            ->createOne();

        $pivotTable = $label->tags()->getTable();

        $this->tagFactory
            ->count(2)
            ->create();

        $label->tags()->sync([1, 2]);

        $this->assertDatabaseCount($label::class, 1)
            ->assertDatabaseCount($this->tag::class, 2)
            ->assertDatabaseCount($pivotTable, 2);

        $this->actingAs($this->userFactory->makeOne())
            ->delete('/labels/'.$label->id)
            ->assertNoContent();

        $this->assertDatabaseCount($label::class, 0)
            ->assertDatabaseCount($this->tag::class, 2)
            ->assertDatabaseCount($pivotTable, 0);
    }
}
