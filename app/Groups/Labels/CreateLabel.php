<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

class CreateLabel extends Controller
{
    public function __construct(
        private readonly Label $label,
        private readonly ResponseFactory $response,
        private readonly UrlGenerator $url,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(CreateLabelRequest $request): Response
    {
        $request->save($this->label);

        return $this->response->make(
            new LabelAdminResource($this->label),
            201,
        )
            ->header('Location', $this->url->to(
                '/labels/'.$this->label->id
            ));
    }
}
