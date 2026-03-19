<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

readonly class GetLabels
{
    public function __construct(
        private Label $label,
        private Request $request,
    ) {
    }

    public function __invoke(): ResourceCollection
    {
        /** @var Label $query */
        $query = $this->label::query()
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
            ->toResourceCollection(LabelResource::class);
    }
}
