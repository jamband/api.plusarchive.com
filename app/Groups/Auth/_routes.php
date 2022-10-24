<?php

declare(strict_types=1);

namespace App\Groups\Auth;

use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;

/** @var RouteRegistrar $router */
$router->prefix('auth')->group(function (Router $router) {
    $router->post('login', Login::class);
    $router->post('logout', Logout::class);
    $router->get('user', GetUser::class);
});
