<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;

readonly class GetStoreCountries
{
    public function __construct(
        private Store $store,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->response->make(
            $this->store->getCountryNames(),
        );
    }
}
