<?php

declare(strict_types=1);

namespace App\Groups\Countries;

use Illuminate\Http\Response;
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

    public function __invoke(int $id): Response
    {
        return $this->response->make(
            new CountryAdminResource(
                $this->country::query()
                    ->findOrFail($id)
            ),
        );
    }
}
