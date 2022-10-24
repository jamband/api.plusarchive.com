<?php

declare(strict_types=1);

namespace App\Groups\Countries;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class UpdateCountry extends Controller
{
    public function __construct(
        private Country $country,
        private ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(
        UpdateCountryRequest $request,
        int $id,
    ): JsonResponse {
        $country = $this->country::query()
            ->findOrFail($id);

        assert($country instanceof Country);

        $request->save($country);

        return $this->response->json(
            data: new CountryAdminResource($country),
        );
    }
}
