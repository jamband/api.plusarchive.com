<?php

declare(strict_types=1);

namespace Tests\Feature\LabelTags;

use App\Groups\LabelTags\LabelTag;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class CreateLabelTagTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('POST /label-tags');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('POST /label-tags');
    }

    public function testCreateLabelTag(): void
    {
        $this->assertDatabaseCount(LabelTag::class, 0);

        $this->actingAs(UserFactory::new()->makeOne())
            ->postJson('/label-tags', [
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

        $this->assertDatabaseCount(LabelTag::class, 1);

        $this->assertDatabaseHas(LabelTag::class, [
            'id' => 1,
            'name' => 'tag1',
        ]);
    }
}
