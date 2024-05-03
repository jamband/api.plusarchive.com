<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\LabelTags;

use App\Groups\LabelTags\LabelTag;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CreateLabelTagTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private LabelTag $tag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->tag = new LabelTag();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->post('/label-tags')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->post('/label-tags')
            ->assertUnauthorized();
    }

    public function testCreateLabelTag(): void
    {
        $this->assertDatabaseCount($this->tag::class, 0);

        $this->actingAs($this->userFactory->makeOne())
            ->post('/label-tags', [
                'name' => 'tag1',
            ])
            ->assertCreated()
            ->assertHeader(
                'Location',
                $this->app['config']['app.url'].'/label-tags/1'
            )
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('id', 1)
                ->where('name', 'tag1'));

        $this->assertDatabaseCount($this->tag::class, 1)
            ->assertDatabaseHas($this->tag::class, [
                'id' => 1,
                'name' => 'tag1',
            ]);
    }
}
