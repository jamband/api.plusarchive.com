<?php

declare(strict_types=1);

namespace App\Groups\Countries;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class UpdateCountry
{
    public function __construct(
        private Country $country,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(UpdateCountryRequest $request, int $id): Response
    {
        $country = $this->country->findOrFail($id);
        $request->save($country);

        return $this->response->make(
            $country->toResource(CountryAdminResource::class),
        );
    }
}
