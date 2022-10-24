<?php

declare(strict_types=1);

namespace App\Groups\Bookmarks;

use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;

/** @var RouteRegistrar $router */
$router->prefix('bookmarks')->group(function (Router $router) {
    $router->pattern('id', '[\d]+');

    $router->get('', GetBookmarks::class);
    $router->post('', CreateBookmark::class);

    $router->get('countries', GetBookmarkCountries::class);
    $router->get('tags', GetBookmarkTags::class);
    $router->get('search', GetSearchBookmarks::class);
    $router->get('admin', GetAdminBookmarks::class);

    $router->get('{id}', GetBookmark::class);
    $router->put('{id}', UpdateBookmark::class);
    $router->delete('{id}', DeleteBookmark::class);
});
