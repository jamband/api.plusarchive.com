<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;

class GetSearchLabels extends Controller
{
    public function __construct(
        private readonly Request $request,
        private readonly Label $label,
    ) {
    }

    public function __invoke(): ResourceCollection
    {
        /** @var Label $query */
        $query = $this->label::query()
            ->with('country')
            ->with('tags');

        $search = $this->request->query('q');
        $search = is_string($search) ? $search : '';

        return $query->ofSearch($search)
            ->inNameOrder()
            ->paginate(14)
            ->toResourceCollection(LabelResource::class);
    }
}
