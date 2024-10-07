<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Bookmarks;

use App\Groups\Bookmarks\BookmarkFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class UpdateBookmarkRulesTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private BookmarkFactory $bookmarkFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->bookmarkFactory = new BookmarkFactory();
    }

    /**
     * @param array<string, mixed> $data
     * @return TestResponse<Response>
     */
    protected function request(array $data): TestResponse
    {
        return $this->actingAs($this->userFactory->makeOne())
            ->put('/bookmarks/1', $data)
            ->assertUnprocessable();
    }

    public function testNameRequiredRule(): void
    {
        $this->request(['name' => null])
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

    public function testNameMaxStringRule(): void
    {
        $this->request(['name' => str_repeat('a', 201)])
            ->assertJsonPath('errors.name', __('validation.max.string', [
                'attribute' => 'name',
                'max' => 200,
            ]));
    }

    public function testCountryRequiredRule(): void
    {
        $this->request(['country' => null])
            ->assertJsonPath('errors.country', __('validation.required', [
                'attribute' => 'country',
            ]));
    }

    public function testCountryExistsRule(): void
    {
        $this->request(['country' => 'foo'])
            ->assertJsonPath('errors.country', __('validation.exists', [
                'attribute' => 'country',
            ]));
    }

    public function testUrlRequiredRule(): void
    {
        $this->request(['url' => null])
            ->assertJsonPath('errors.url', __('validation.required', [
                'attribute' => 'url',
            ]));
    }

    public function testUrlStringRule(): void
    {
        $this->request(['url' => 1])
            ->assertJsonPath('errors.url', __('validation.string', [
                'attribute' => 'url',
            ]));
    }

    public function testUrlUrlRule(): void
    {
        $this->request(['url' => 'foo'])
            ->assertJsonPath('errors.url', __('validation.url', [
                'attribute' => 'url',
            ]));
    }

    public function testUrlUniqueRule(): void
    {
        $this->bookmarkFactory
            ->count(2)
            ->state(new Sequence(
                ['url' => 'https://url1.dev'],
                ['url' => 'https://url2.dev'],
            ))
            ->create();


        $this->request(['url' => 'https://url2.dev'])
            ->assertJsonPath('errors.url', __('validation.unique', [
                'attribute' => 'url',
            ]));
    }

    public function testLinksStringRule(): void
    {
        $this->request(['links' => 1])
            ->assertJsonPath('errors.links', __('validation.string', [
                'attribute' => 'links',
            ]));
    }

    public function testLinksMaxStringRule(): void
    {
        $this->request(['links' => str_repeat('a', 1001)])
            ->assertJsonPath('errors.links', __('validation.max.string', [
                'attribute' => 'links',
                'max' => 1000,
            ]));
    }

    public function testLinksMultipleUrlsRule(): void
    {
        $this->request(['links' => 'foo'])
            ->assertJsonPath('errors.links', __('validation.multiple_urls', [
                'attribute' => 'links',
            ]));
    }

    public function testTagsTaggableRule(): void
    {
        $this->request(['tags' => 'foo'])
            ->assertJsonPath('errors.tags', __('validation.taggables.array', [
                'attribute' => 'tags',
            ]));
    }
}
