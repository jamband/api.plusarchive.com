<?php

declare(strict_types=1);

namespace App\Groups\BookmarkTags;

use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;

/** @var RouteRegistrar $router */
$router->prefix('bookmark-tags')->group(function (Router $router) {
    $router->pattern('id', '[\d]+');

    $router->post('', CreateBookmarkTag::class);

    $router->get('admin', GetAdminBookmarkTags::class);

    $router->get('{id}', GetBookmarkTag::class);
    $router->put('{id}', UpdateBookmarkTag::class);
    $router->delete('{id}', DeleteBookmarkTag::class);
});
