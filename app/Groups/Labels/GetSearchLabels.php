<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GetSearchLabels extends Controller
{
    public function __construct(
        private readonly Request $request,
        private readonly Label $label,
    ) {
    }

    public function __invoke(): LabelResourceCollection
    {
        /** @var Label $query */
        $query = $this->label::query()
            ->with('tags');

        $search = $this->request->query('q');
        $search = is_string($search) ? $search : '';

        return new LabelResourceCollection(
            $query->ofSearch($search)
                ->inNameOrder()
                ->paginate(14)
        );
    }
}
