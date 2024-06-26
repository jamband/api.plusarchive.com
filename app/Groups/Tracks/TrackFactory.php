<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use App\Groups\MusicProviders\MusicProviderFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Track>
 */
class TrackFactory extends Factory
{
    protected $model = Track::class;

    public function definition(): array
    {
        $providerFactory = new MusicProviderFactory();
        $carbon = new Carbon();

        return [
            'url' => $this->faker->url(),
            'provider_id' => $providerFactory,
            'provider_key' => Str::random(10),
            'title' => $this->faker->title(),
            'image' => $this->faker->url(),
            'urge' => false,
            'created_at' => $carbon,
            'updated_at' => $carbon,
        ];
    }
}
