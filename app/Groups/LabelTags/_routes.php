<?php

declare(strict_types=1);

namespace App\Groups\LabelTags;

use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;

/** @var RouteRegistrar $router */
$router->prefix('label-tags')->group(function (Router $router) {
    $router->pattern('id', '[\d]+');

    $router->post('', CreateLabelTag::class);

    $router->get('admin', GetAdminLabelTags::class);

    $router->get('{id}', GetLabelTag::class);
    $router->put('{id}', UpdateLabelTag::class);
    $router->delete('{id}', DeleteLabelTag::class);
});
