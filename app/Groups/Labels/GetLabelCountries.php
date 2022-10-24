<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetLabelCountries extends Controller
{
    public function __construct(
        private Label $label,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        return $this->response->json(
            data: $this->label->getCountryNames(),
        );
    }
}
