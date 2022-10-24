<?php

declare(strict_types=1);

namespace App\Groups\LabelTags;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GetAdminLabelTags extends Controller
{
    public function __construct(
        private LabelTag $tag,
        private Request $request,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(): LabelTagAdminResourceCollection
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

        return new LabelTagAdminResourceCollection(
            $query->paginate(24)
        );
    }
}
