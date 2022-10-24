<?php

declare(strict_types=1);

namespace App\Groups\Playlists;

use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;

/** @var RouteRegistrar $router */
$router->prefix('playlists')->group(function (Router $router) {
    $router->pattern('hash', '[\w-]{11}');

    $router->get('', GetPlaylists::class);
    $router->post('', CreatePlaylist::class);

    $router->get('admin', GetAdminPlaylists::class);

    $router->get('{hash}', GetPlaylist::class);
    $router->put('{hash}', UpdatePlaylist::class);
    $router->delete('{hash}', DeletePlaylist::class);
});
