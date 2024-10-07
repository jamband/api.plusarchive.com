<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\TrackGenres;

use App\Groups\TrackGenres\TrackGenreFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class UpdateTrackGenreRulesTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private TrackGenreFactory $genreFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->genreFactory = new TrackGenreFactory();
    }

    /**
     * @param array<string, mixed> $data
     * @return TestResponse<Response>
     */
    protected function request(array $data): TestResponse
    {
        return $this->actingAs($this->userFactory->makeOne())
            ->put('/track-genres/1', $data)
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

    public function testNameTaggableRule(): void
    {
        $this->request(['name' => '!'])
            ->assertJsonPath('errors.name', __('validation.taggable', [
                'attribute' => 'name',
            ]));
    }

    public function testNameUniqueRule(): void
    {
        $this->genreFactory
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
