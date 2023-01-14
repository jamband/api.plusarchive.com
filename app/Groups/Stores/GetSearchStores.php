<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GetSearchStores extends Controller
{
    public function __construct(
        private readonly Request $request,
        private readonly Store $store,
    ) {
    }

    public function __invoke(): StoreResourceCollection
    {
        /** @var Store $query */
        $query = $this->store::query()
            ->with('tags');

        $search = $this->request->query('q');
        $search = is_string($search) ? $search : '';

        return new StoreResourceCollection(
            $query->ofSearch($search)
                ->inNameOrder()
                ->paginate(14)
        );
    }
}
