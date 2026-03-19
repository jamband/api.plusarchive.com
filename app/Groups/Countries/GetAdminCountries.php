<?php

declare(strict_types=1);

namespace App\Groups\Countries;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Attributes\Controllers\Middleware;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class GetAdminCountries
{
    public function __construct(
        private Country $country,
        private Request $request,
    ) {
    }

    public function __invoke(): ResourceCollection
    {
        /** @var Country $query */
        $query = $this->country::query();

        $name = $this->request->query('name');
        if (is_string($name)) {
            $query->ofSearch($name);
        }

        $sort = $this->request->query('sort');

        $sortableColumn = [
            'id',
            'name',
        ];

        if (
            is_string($sort) &&
            in_array(trim($sort, '-'), $sortableColumn, true)
        ) {
            $query->sort($sort);
        } else {
            $query->latest('id');
        }

        return $query->get()
            ->toResourceCollection(CountryAdminResource::class);
    }
}
