<?php

declare(strict_types=1);

namespace App\Groups\StoreTags;

use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;

/** @var RouteRegistrar $router */
$router->prefix('store-tags')->group(function (Router $router) {
    $router->pattern('id', '[\d]+');

    $router->post('', CreateStoreTag::class);

    $router->get('admin', GetAdminStoreTags::class);

    $router->get('{id}', GetStoreTag::class);
    $router->put('{id}', UpdateStoreTag::class);
    $router->delete('{id}', DeleteStoreTag::class);
});
