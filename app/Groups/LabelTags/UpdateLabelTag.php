<?php

declare(strict_types=1);

namespace App\Groups\LabelTags;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class UpdateLabelTag
{
    public function __construct(
        private LabelTag $tag,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(UpdateLabelTagRequest $request, int $id): Response
    {
        $tag = $this->tag->findOrFail($id);
        $request->save($tag);

        return $this->response->make(
            $tag->toResource(LabelTagAdminResource::class),
        );
    }
}
