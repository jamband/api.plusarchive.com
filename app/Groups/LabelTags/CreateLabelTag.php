<?php

declare(strict_types=1);

namespace App\Groups\LabelTags;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

class CreateLabelTag extends Controller
{
    public function __construct(
        private readonly LabelTag $tag,
        private readonly ResponseFactory $response,
        private readonly UrlGenerator $url,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(
        CreateLabelTagRequest $request
    ): JsonResponse {
        $request->save($this->tag);

        return $this->response->json(
            data: new LabelTagAdminResource($this->tag),
            status: 201,
        )
            ->header('Location', $this->url->to(
                '/label-tags/'.$this->tag->id
            ));
    }
}
