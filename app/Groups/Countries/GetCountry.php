<?php

declare(strict_types=1);

namespace App\Groups\Countries;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetCountry extends Controller
{
    public function __construct(
        private readonly Country $country,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(int $id): JsonResponse
    {
        return $this->response->json(
            data: new CountryAdminResource(
                $this->country::query()
                    ->findOrFail($id)
            ),
        );
    }
}
