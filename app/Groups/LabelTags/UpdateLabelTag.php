<?php

declare(strict_types=1);

namespace App\Groups\LabelTags;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class UpdateLabelTag extends Controller
{
    public function __construct(
        private readonly LabelTag $tag,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
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
