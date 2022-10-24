<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use App\Groups\StoreTags\StoreTag;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetStoreTags extends Controller
{
    public function __construct(
        private ResponseFactory $response,
        private StoreTag $tag,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        return $this->response->json(
            data: $this->tag->getNames(),
        );
    }
}
