<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Playlists;

use App\Groups\Playlists\PlaylistFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Jamband\Ripple\Ripple;
use Tests\TestCase;

class CreatePlaylistRulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $ripple = $this->app->make(Ripple::class);
        assert($ripple instanceof Ripple);
        $ripple->options(['response' => '']);
        $this->instance(Ripple::class, $ripple);
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function request(array $data = []): TestResponse
    {
        return $this->actingAs(UserFactory::new()->makeOne())
            ->postJson('/playlists', $data)
            ->assertUnprocessable();
    }

    public function testUrlRequiredRule(): void
    {
        $this->request()
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

    public function testUrlUniqueRule(): void
    {
        $playlist = PlaylistFactory::new()
            ->createOne();

        $this->request(['url' => $playlist->url])
            ->assertJsonPath('errors.url', __('validation.unique', [
                'attribute' => 'url',
            ]));
    }

    public function testUrlRippleUrlRule(): void
    {
        $this->request(['url' => 'https://example.com'])
            ->assertJsonPath('errors.url', __('validation.ripple.url', [
                'attribute' => 'url',
            ]));
    }

    public function testUrlRippleImageRule(): void
    {
        $this->request(['url' => 'https://soundcloud.com/foo/sets/bar'])
            ->assertJsonPath('errors.url', __('validation.ripple.image', [
                'attribute' => 'url',
            ]));
    }

    public function testTitleStringRule(): void
    {
        $this->request(['title' => 1])
            ->assertJsonPath('errors.title', __('validation.string', [
                'attribute' => 'title',
            ]));
    }

    public function testTitleMaxStringRule(): void
    {
        $this->request(['title' => str_repeat('a', 201)])
            ->assertJsonPath('errors.title', __('validation.max.string', [
                'attribute' => 'title',
                'max' => 200,
            ]));
    }
}
