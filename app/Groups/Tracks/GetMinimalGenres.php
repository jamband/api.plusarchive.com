<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;

readonly class GetMinimalGenres
{
    public function __construct(
        private Track $track,
        private Request $request,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(): Response
    {
        $limit = $this->request->query('limit');
        $limit = is_string($limit) ? (int)$limit : 10;

        return $this->response->make(
            $this->track->getMinimalGenres($limit),
        );
    }
}
