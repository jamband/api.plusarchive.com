<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GetLabels extends Controller
{
    public function __construct(
        private Label $label,
        private Request $request,
    ) {
    }

    public function __invoke(): LabelResourceCollection
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

        return new LabelResourceCollection(
            $query->latest()->paginate(14)
        );
    }
}
