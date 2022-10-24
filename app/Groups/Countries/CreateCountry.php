<?php

declare(strict_types=1);

namespace App\Groups\Countries;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

class CreateCountry extends Controller
{
    public function __construct(
        private Country $country,
        private ResponseFactory $response,
        private UrlGenerator $url,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(
        CreateCountryRequest $request,
    ): JsonResponse {
        $request->save($this->country);

        return $this->response->json(
            data: new CountryAdminResource($this->country),
            status: 201,
        )
            ->header('Location', $this->url->to(
                '/countries/'.$this->country->id
            ));
    }
}
