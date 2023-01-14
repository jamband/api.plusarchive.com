<?php

declare(strict_types=1);

namespace App\Groups\Countries;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GetAdminCountries extends Controller
{
    public function __construct(
        private readonly Country $country,
        private readonly Request $request,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(): CountryAdminResourceCollection
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

        return new CountryAdminResourceCollection(
            $query->get()
        );
    }
}
