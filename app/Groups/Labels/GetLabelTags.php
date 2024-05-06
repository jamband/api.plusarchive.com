<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use App\Groups\LabelTags\LabelTag;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetLabelTags extends Controller
{
    public function __construct(
        private readonly ResponseFactory $response,
        private readonly LabelTag $tag,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->response->make(
            $this->tag->getNames(),
        );
    }
}
