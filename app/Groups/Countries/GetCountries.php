<?php

declare(strict_types=1);

namespace App\Groups\Countries;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetCountries extends Controller
{
    public function __construct(
        private readonly Country $country,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(): JsonResponse
    {
        return $this->response->json(
            data: $this->country->getNames(),
        );
    }
}
