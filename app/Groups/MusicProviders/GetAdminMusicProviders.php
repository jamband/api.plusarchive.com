<?php

declare(strict_types=1);

namespace App\Groups\MusicProviders;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GetAdminMusicProviders extends Controller
{
    public function __construct(
        private MusicProvider $provider,
        private Request $request,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(): MusicProviderAdminResourceCollection
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

        return new MusicProviderAdminResourceCollection(
            $query->get()
        );
    }
}
