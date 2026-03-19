<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class GetLabel
{
    public function __construct(
        private Label $label,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(int $id): Response
    {
        return $this->response->make(
            $this->label::query()
                ->with('country')
                ->with('tags')
                ->findOrFail($id)
                ->toResource(LabelAdminResource::class),
        );
    }
}
