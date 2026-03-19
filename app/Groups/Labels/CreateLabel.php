<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class CreateLabel
{
    public function __construct(
        private Label $label,
        private ResponseFactory $response,
        private UrlGenerator $url,
    ) {
    }

    public function __invoke(CreateLabelRequest $request): Response
    {
        $request->save($this->label);

        return $this->response->make(
            $this->label->toResource(LabelAdminResource::class),
            201,
        )
            ->header('Location', $this->url->to(
                '/labels/'.$this->label->id
            ));
    }
}
