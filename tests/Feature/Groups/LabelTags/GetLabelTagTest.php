<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\LabelTags;

use App\Groups\LabelTags\LabelTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetLabelTagTest extends TestCase
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
            ->get('/label-tags/1')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->get('/label-tags/1')
            ->assertUnauthorized();
    }

    public function testModelNotFound(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->get('/label-tags/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testGetLabelTag(): void
    {
        $tag = $this->tagFactory
            ->createOne();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/label-tags/'.$tag->id)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($tag) {
                $json->where('id', $tag->id)
                    ->where('name', $tag->name);
            });
    }
}
