<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetStoreCountries extends Controller
{
    public function __construct(
        private readonly Store $store,
        private readonly ResponseFactory $response,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->response->make(
            $this->store->getCountryNames(),
        );
    }
}
