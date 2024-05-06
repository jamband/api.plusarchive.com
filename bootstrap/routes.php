<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Routing\RouteRegistrar;

return function (Application $app, RouteRegistrar $router) {
    $groups = [
        'Site',
        'Auth',
        'Bookmarks',
        'BookmarkTags',
        'Countries',
        'Labels',
        'LabelTags',
        'MusicProviders',
        'Playlists',
        'Stores',
        'StoreTags',
        'Tracks',
        'TrackGenres',
    ];

    foreach ($groups as $group) {
        $router->middleware('web')->group(
            $app->basePath('app/Groups/'.$group.'/_routes.php')
        );
    }
};
