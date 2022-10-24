<?php

declare(strict_types=1);

namespace Tests\Feature\BookmarkTags;

use App\Groups\BookmarkTags\BookmarkTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class UpdateBookmarkTagRulesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param array<string, mixed> $data
     */
    protected function request(array $data = []): TestResponse
    {
        return $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/bookmark-tags/1', $data)
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
        BookmarkTagFactory::new()
            ->count(2)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
            ))
            ->create();

        $this->request(['name' => 'bar'])
            ->assertJsonPath('errors.name', __('validation.unique', [
                'attribute' => 'name',
            ]));
    }
}
