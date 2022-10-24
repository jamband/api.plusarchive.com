<?php

declare(strict_types=1);

namespace App\Groups\TrackGenres;

use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;

/** @var RouteRegistrar $router */
$router->prefix('track-genres')->group(function (Router $router) {
    $router->pattern('id', '[\d]+');

    $router->post('', CreateTrackGenre::class);

    $router->get('admin', GetAdminTrackGenres::class);

    $router->get('{id}', GetTrackGenre::class);
    $router->put('{id}', UpdateTrackGenre::class);
    $router->delete('{id}', DeleteTrackGenre::class);
});
