<?php

declare(strict_types=1);

namespace Tests\Feature\Labels;

use App\Groups\Labels\LabelFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class GetLabelTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('GET /labels/1');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('GET /labels/1');
    }

    public function testModelNotFound(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/labels/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testGetLabel(): void
    {
        $label = LabelFactory::new()
            ->createOne();

        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/labels/'.$label->id)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($label) {
                $json->where('id', $label->id)
                    ->where('name', $label->name)
                    ->where('country', $label->country->name)
                    ->where('url', $label->url)
                    ->where('links', $label->links)
                    ->where('tags', [])
                    ->where('created_at', $label->created_at->format('Y-m-d H:i'))
                    ->where('updated_at', $label->updated_at->format('Y-m-d H:i'));
            });
    }
}
