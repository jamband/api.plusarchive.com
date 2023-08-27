<?php

declare(strict_types=1);

namespace App\Groups\Countries;

use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;

/** @var RouteRegistrar $router */
$router->prefix('countries')->group(function (Router $router) {
    $router->pattern('id', '[\d]+');

    $router->get('', GetCountries::class);
    $router->post('', CreateCountry::class);

    $router->get('admin', GetAdminCountries::class);

    $router->get('{id}', GetCountry::class);
    $router->put('{id}', UpdateCountry::class);
    $router->delete('{id}', DeleteCountry::class);
});
