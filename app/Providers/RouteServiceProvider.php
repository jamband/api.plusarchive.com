<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\RouteRegistrar;

class RouteServiceProvider extends ServiceProvider
{
    public function map(RouteRegistrar $router): void
    {
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
            $router->middleware('web')
                ->group($this->app->basePath(
                    'app/Groups/'.$group.'/_routes.php'
                ));
        }
    }
}
