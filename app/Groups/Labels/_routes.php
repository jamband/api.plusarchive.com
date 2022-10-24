<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;

/** @var RouteRegistrar $router */
$router->prefix('labels')->group(function (Router $router) {
    $router->pattern('id', '[\d]+');

    $router->get('', GetLabels::class);
    $router->post('', CreateLabel::class);

    $router->get('countries', GetLabelCountries::class);
    $router->get('tags', GetLabelTags::class);
    $router->get('search', GetSearchLabels::class);
    $router->get('admin', GetAdminLabels::class);

    $router->get('{id}', GetLabel::class);
    $router->put('{id}', UpdateLabel::class);
    $router->delete('{id}', DeleteLabel::class);
});
