<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use App\Groups\StoreTags\StoreTag;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;

readonly class GetStoreTags
{
    public function __construct(
        private ResponseFactory $response,
        private StoreTag $tag,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->response->make(
            $this->tag->getNames(),
        );
    }
}
