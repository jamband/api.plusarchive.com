<?php

declare(strict_types=1);

namespace App\Groups\LabelTags;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class GetLabelTag
{
    public function __construct(
        private LabelTag $tag,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(int $id): Response
    {
        return $this->response->make(
            $this->tag::query()
                ->findOrFail($id),
        );
    }
}
