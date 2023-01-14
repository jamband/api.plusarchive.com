<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GetAdminStores extends Controller
{
    public function __construct(
        private readonly Store $store,
        private readonly Request $request,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(): StoreAdminResourceCollection
    {
        /** @var Store $query */
        $query = $this->store::query()
            ->with('country')
            ->with('tags');

        $name = $this->request->query('name');
        if (is_string($name)) {
            $query->ofSearch($name);
        }

        $country = $this->request->query('country');
        if (is_string($country)) {
            $query->ofCountry($country);
        }

        $tag = $this->request->query('tag');
        if (is_string($tag)) {
            $query->ofTag($tag);
        }

        $sort = $this->request->query('sort');

        $sortableColumns = [
            'name',
            $this->store->getCreatedAtColumn(),
            $this->store->getUpdatedAtColumn(),
        ];

        if (
            is_string($sort) &&
            in_array(trim($sort, '-'), $sortableColumns, true)
        ) {
            $query->sort($sort);
        } else {
            $query->latest();
        }

        return new StoreAdminResourceCollection(
            $query->paginate(24)
        );
    }
}
