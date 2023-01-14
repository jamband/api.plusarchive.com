<?php

declare(strict_types=1);

namespace App\Groups\TrackGenres;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GetAdminTrackGenres extends Controller
{
    public function __construct(
        private readonly TrackGenre $genre,
        private readonly Request $request,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(): TrackGenreAdminResourceCollection
    {
        /** @var TrackGenre $query */
        $query = $this->genre::query();

        $name = $this->request->query('name');
        if (is_string($name)) {
            $query->ofSearch($name);
        }

        $sort = $this->request->query('sort');

        $sortableColumns = [
            'id',
            'name',
        ];

        if (
            is_string($sort) &&
            in_array(trim($sort, '-'), $sortableColumns, true)
        ) {
            $query->sort($sort);
        } else {
            $query->latest('id');
        }

        return new TrackGenreAdminResourceCollection(
            $query->paginate(24)
        );
    }
}
