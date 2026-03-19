<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Attributes\Controllers\Middleware;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class GetAdminLabels
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
            $this->label->getCreatedAtColumn(),
            $this->label->getUpdatedAtColumn(),
        ];

        if (
            is_string($sort) &&
            in_array(trim($sort, '-'), $sortableColumns, true)
        ) {
            $query->sort($sort);
        } else {
            $query->latest();
        }

        return $query->paginate(24)
            ->toResourceCollection(LabelAdminResource::class);
    }
}
