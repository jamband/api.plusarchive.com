<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;

/** @var RouteRegistrar $router */
$router->prefix('stores')->group(function (Router $router) {
    $router->pattern('id', '[\d]+');

    $router->get('', GetStores::class);
    $router->post('', CreateStore::class);

    $router->get('countries', GetStoreCountries::class);
    $router->get('tags', GetStoreTags::class);
    $router->get('search', GetSearchStores::class);
    $router->get('admin', GetAdminStores::class);

    $router->get('{id}', GetStore::class);
    $router->put('{id}', UpdateStore::class);
    $router->delete('{id}', DeleteStore::class);
});
