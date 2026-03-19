<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class DeleteLabel
{
    public function __construct(
        private Label $label,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(int $id): Response
    {
        $this->label::query()
            ->findOrFail($id)
            ->delete();

        return $this->response->noContent();
    }
}
