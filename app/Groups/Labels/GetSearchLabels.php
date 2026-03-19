<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

readonly class GetSearchLabels
{
    public function __construct(
        private Request $request,
        private Label $label,
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
