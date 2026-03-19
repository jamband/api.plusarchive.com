<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class UpdateLabel
{
    public function __construct(
        private Label $label,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(UpdateLabelRequest $request, int $id): Response
    {
        $label = $this->label->findOrFail($id);
        $request->save($label);

        return $this->response->make(
            $label->toResource(LabelAdminResource::class),
        );
    }
}
