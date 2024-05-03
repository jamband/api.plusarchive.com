<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\TrackGenres;

use App\Groups\TrackGenres\TrackGenreFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetTrackGenreTest extends TestCase
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

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->get('/track-genres/1')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->get('/track-genres/1')
            ->assertUnauthorized();
    }

    public function testModelNotFound(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->get('/track-genres/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testGetGenre(): void
    {
        $genre = $this->genreFactory
            ->createOne();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/track-genres/1')
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($genre) {
                $json->where('id', $genre->id)
                    ->where('name', $genre->name);
            });
    }
}
