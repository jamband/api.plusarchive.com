<?php

declare(strict_types=1);

namespace App\Groups\Bookmarks;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetBookmarkCountries extends Controller
{
    public function __construct(
        private readonly Bookmark $bookmark,
        private readonly ResponseFactory $response,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        return $this->response->json(
            data: $this->bookmark->getCountryNames(),
        );
    }
}
