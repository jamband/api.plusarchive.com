<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetMinimalGenres extends Controller
{
    public function __construct(
        private readonly Track $track,
        private readonly Request $request,
        private readonly ResponseFactory $response,
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
