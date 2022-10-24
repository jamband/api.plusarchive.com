<?php

declare(strict_types=1);

namespace App\Groups\LabelTags;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetLabelTag extends Controller
{
    public function __construct(
        private LabelTag $tag,
        private ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(int $id): JsonResponse
    {
        return $this->response->json(
            data: $this->tag::query()
                ->findOrFail($id),
        );
    }
}
