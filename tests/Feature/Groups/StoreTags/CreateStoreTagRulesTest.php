<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\StoreTags;

use App\Groups\StoreTags\StoreTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class CreateStoreTagRulesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param array<string, mixed> $data
     */
    protected function request(array $data = []): TestResponse
    {
        return $this->actingAs(UserFactory::new()->makeOne())
            ->postJson('/store-tags', $data)
            ->assertUnprocessable();
    }

    public function testNameRequiredRule(): void
    {
        $this->request()
            ->assertJsonPath('errors.name', __('validation.required', [
                'attribute' => 'name',
            ]));
    }

    public function testNameStringRule(): void
    {
        $this->request(['name' => 1])
            ->assertJsonPath('errors.name', __('validation.string', [
                'attribute' => 'name',
            ]));
    }

    public function testNameTaggableRule(): void
    {
        $this->request(['name' => '!'])
            ->assertJsonPath('errors.name', __('validation.taggable', [
                'attribute' => 'name',
            ]));
    }

    public function testNameUniqueRule(): void
    {
        $tag = StoreTagFactory::new()
            ->createOne();

        $this->request(['name' => $tag->name])
            ->assertJsonPath('errors.name', __('validation.unique', [
                'attribute' => 'name',
            ]));
    }
}
