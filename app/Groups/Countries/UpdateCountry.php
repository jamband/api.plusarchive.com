<?php

declare(strict_types=1);

namespace App\Groups\Countries;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class UpdateCountry extends Controller
{
    public function __construct(
        private readonly Country $country,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(UpdateCountryRequest $request, int $id): Response
    {
        $country = $this->country::query()
            ->findOrFail($id);

        assert($country instanceof Country);

        $request->save($country);

        return $this->response->make(
            new CountryAdminResource($country),
        );
    }
}
