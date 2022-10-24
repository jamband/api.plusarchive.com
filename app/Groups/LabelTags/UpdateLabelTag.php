<?php

declare(strict_types=1);

namespace App\Groups\LabelTags;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class UpdateLabelTag extends Controller
{
    public function __construct(
        private LabelTag $tag,
        private ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(
        UpdateLabelTagRequest $request,
        int $id,
    ): JsonResponse {
        $tag = $this->tag::query()
            ->findOrFail($id);

        assert($tag instanceof LabelTag);

        $request->save($tag);

        return $this->response->json(
            data: new LabelTagAdminResource($tag),
        );
    }
}
