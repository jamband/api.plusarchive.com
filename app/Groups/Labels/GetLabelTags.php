<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use App\Groups\LabelTags\LabelTag;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;

readonly class GetLabelTags
{
    public function __construct(
        private ResponseFactory $response,
        private LabelTag $tag,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->response->make(
            $this->tag->getNames(),
        );
    }
}
