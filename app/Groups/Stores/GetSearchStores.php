<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;

class GetSearchStores extends Controller
{
    public function __construct(
        private readonly Request $request,
        private readonly Store $store,
    ) {
    }

    public function __invoke(): ResourceCollection
    {
        /** @var Store $query */
        $query = $this->store::query()
            ->with('country')
            ->with('tags');

        $search = $this->request->query('q');
        $search = is_string($search) ? $search : '';

        return $query->ofSearch($search)
            ->inNameOrder()
            ->paginate(14)
            ->toResourceCollection(StoreResource::class);
    }
}
