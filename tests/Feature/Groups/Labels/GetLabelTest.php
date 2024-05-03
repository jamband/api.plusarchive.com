<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Labels;

use App\Groups\Labels\LabelFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetLabelTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private LabelFactory $labelFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->labelFactory = new LabelFactory();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->get('/labels/1')
            ->assertConflict();

    }

    public function testAuthMiddleware(): void
    {
        $this->get('/labels/1')
            ->assertUnauthorized();
    }

    public function testModelNotFound(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->get('/labels/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testGetLabel(): void
    {
        $label = $this->labelFactory
            ->createOne();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/labels/'.$label->id)
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
