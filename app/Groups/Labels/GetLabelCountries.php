<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;

readonly class GetLabelCountries
{
    public function __construct(
        private Label $label,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->response->make(
            $this->label->getCountryNames(),
        );
    }
}
