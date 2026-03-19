<?php

declare(strict_types=1);

namespace App\Groups\Bookmarks;

use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;

readonly class GetBookmarkCountries
{
    public function __construct(
        private Bookmark $bookmark,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->response->make(
            $this->bookmark->getCountryNames(),
        );
    }
}
