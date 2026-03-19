<?php

declare(strict_types=1);

namespace App\Groups\Countries;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class CreateCountry
{
    public function __construct(
        private Country $country,
        private ResponseFactory $response,
        private UrlGenerator $url,
    ) {
    }

    public function __invoke(CreateCountryRequest $request): Response
    {
        $request->save($this->country);

        return $this->response->make(
            $this->country->toResource(CountryAdminResource::class),
            201,
        )
            ->header('Location', $this->url->to(
                '/countries/'.$this->country->id
            ));
    }
}
