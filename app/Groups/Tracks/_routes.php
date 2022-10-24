<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;

/** @var RouteRegistrar $router */
$router->prefix('tracks')->group(function (Router $router) {
    $router->pattern('hash', '[\w-]{11}');

    $router->get('', GetTracks::class);
    $router->post('', CreateTrack::class);

    $router->get('admin', GetAdminTracks::class);
    $router->get('favorites', GetFavoriteTracks::class);
    $router->get('minimal-genres', GetMinimalGenres::class);
    $router->get('providers', GetTrackProviders::class);
    $router->get('genres', GetTrackGenres::class);
    $router->get('search', GetSearchTracks::class);
    $router->patch('stop-all-urges', StopAllUrges::class);

    $router->get('{hash}', GetTrack::class);
    $router->put('{hash}', UpdateTrack::class);
    $router->delete('{hash}', DeleteTrack::class);

    $router->patch('{hash}/toggle-urge', ToggleUrgeTrack::class);
});
