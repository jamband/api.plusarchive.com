<?php

declare(strict_types=1);

namespace App\Groups\Site;

use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;

readonly class GetCsrfCookie
{
    public function __construct(
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->response->noContent();
    }
}
