<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\LabelTags;

use App\Groups\LabelTags\LabelTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteLabelTagTest extends TestCase
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
            ->delete('/label-tags/1')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->delete('/label-tags/1')
            ->assertUnauthorized();
    }

    public function testModelNotFound(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->delete('/label-tags/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testDeleteLabelTag(): void
    {
        $tag = $this->tagFactory
            ->createOne();

        $this->assertDatabaseCount($tag::class, 1);

        $this->actingAs($this->userFactory->makeOne())
            ->delete('/label-tags/'.$tag->id)
            ->assertNoContent();

        $this->assertDatabaseCount($tag::class, 0);
    }
}
