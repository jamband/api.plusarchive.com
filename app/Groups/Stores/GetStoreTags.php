<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use App\Groups\StoreTags\StoreTag;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetStoreTags extends Controller
{
    public function __construct(
        private readonly ResponseFactory $response,
        private readonly StoreTag $tag,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->response->make(
            $this->tag->getNames(),
        );
    }
}
