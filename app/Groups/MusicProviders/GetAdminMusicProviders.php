<?php

declare(strict_types=1);

namespace App\Groups\MusicProviders;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Attributes\Controllers\Middleware;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class GetAdminMusicProviders
{
    public function __construct(
        private MusicProvider $provider,
        private Request $request,
    ) {
    }

    public function __invoke(): ResourceCollection
    {
        /** @var MusicProvider $query */
        $query = $this->provider::query();

        $name = $this->request->query('name');
        if (is_string($name)) {
            $query->ofSearch($name);
        }

        $sort = $this->request->query('sort');

        if (
            is_string($sort) &&
            in_array(trim($sort, '-'), ['id', 'name'], true)
        ) {
            $query->sort($sort);
        } else {
            $query->latest('id');
        }

        return $query->get()
            ->toResourceCollection(MusicProviderAdminResource::class);
    }
}
