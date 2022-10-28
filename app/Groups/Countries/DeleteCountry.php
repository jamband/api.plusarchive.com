<?php

declare(strict_types=1);

namespace App\Groups\Countries;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class DeleteCountry extends Controller
{
    public function __construct(
        private Country $country,
        private ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(int $id): JsonResponse
    {
        $this->country::query()
            ->findOrFail($id)
            ->delete();

        return $this->response->json(
            status: 204,
        );
    }
}