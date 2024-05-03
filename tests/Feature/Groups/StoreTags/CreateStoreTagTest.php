<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\StoreTags;

use App\Groups\StoreTags\StoreTag;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CreateStoreTagTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private StoreTag $tag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->tag = new StoreTag();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->post('/store-tags')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->post('/store-tags')
            ->assertUnauthorized();
    }

    public function testCreateStoreTag(): void
    {
        $this->assertDatabaseCount($this->tag::class, 0);

        $this->actingAs($this->userFactory->makeOne())
            ->post('/store-tags', [
                'name' => 'tag1',
            ])
            ->assertCreated()
            ->assertHeader(
                'Location',
                $this->app['config']['app.url'].'/store-tags/1'
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
