<?php

declare(strict_types=1);

namespace App\Groups\LabelTags;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;

class GetAdminLabelTags extends Controller
{
    public function __construct(
        private readonly LabelTag $tag,
        private readonly Request $request,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(): ResourceCollection
    {
        /** @var LabelTag $query */
        $query = $this->tag::query();

        $name = $this->request->query('name');
        if (is_string($name)) {
            $query->ofSearch($name);
        }

        $sort = $this->request->query('sort');

        $sortableColumns = [
            'id',
            'name',
        ];

        if (
            is_string($sort) &&
            in_array(trim($sort, '-'), $sortableColumns, true)
        ) {
            $query->sort($sort);
        } else {
            $query->latest('id');
        }

        return $query->paginate(24)
            ->toResourceCollection(LabelTagAdminResource::class);
    }
}
