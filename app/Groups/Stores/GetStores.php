<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;

class GetStores extends Controller
{
    public function __construct(
        private readonly Store $store,
        private readonly Request $request,
    ) {
    }

    public function __invoke(): ResourceCollection
    {
        /** @var Store $query */
        $query = $this->store::query()
            ->with('country')
            ->with('tags');

        $country = $this->request->query('country');
        if (is_string($country)) {
            $query->ofCountry($country);
        }

        $tag = $this->request->query('tag');
        if (is_string($tag)) {
            $query->ofTag($tag);
        }

        return $query->latest()
            ->paginate(14)
            ->toResourceCollection(StoreResource::class);
    }
}
