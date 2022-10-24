<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetStoreCountries extends Controller
{
    public function __construct(
        private Store $store,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        return $this->response->json(
            data: $this->store->getCountryNames(),
        );
    }
}
