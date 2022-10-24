<?php

declare(strict_types=1);

namespace App\Groups\Playlists;

use App\Groups\MusicProviders\MusicProviderFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Playlist>
 */
class PlaylistFactory extends Factory
{
    protected $model = Playlist::class;

    public function definition(): array
    {
        return [
            'url' => $this->faker->url(),
            'provider_id' => MusicProviderFactory::new(),
            'provider_key' => Str::random(10),
            'title' => $this->faker->title(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
