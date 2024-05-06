<?php

declare(strict_types=1);

namespace App\Groups\Countries;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

class CreateCountry extends Controller
{
    public function __construct(
        private readonly Country $country,
        private readonly ResponseFactory $response,
        private readonly UrlGenerator $url,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(CreateCountryRequest $request): Response
    {
        $request->save($this->country);

        return $this->response->make(
            new CountryAdminResource($this->country),
            201,
        )
            ->header('Location', $this->url->to(
                '/countries/'.$this->country->id
            ));
    }
}
