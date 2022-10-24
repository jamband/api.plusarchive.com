<?php

declare(strict_types=1);

namespace App\Groups\MusicProviders;

use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;

/** @var RouteRegistrar $router */
$router->prefix('music-providers')->group(function (Router $router) {
    $router->pattern('id', '[\d]+');

    $router->post('', CreateMusicProvider::class);

    $router->get('admin', GetAdminMusicProviders::class);

    $router->get('{id}', GetMusicProvider::class);
    $router->put('{id}', UpdateMusicProvider::class);
    $router->delete('{id}', DeleteMusicProvider::class);
});
