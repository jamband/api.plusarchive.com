<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

class CreateLabel extends Controller
{
    public function __construct(
        private Label $label,
        private ResponseFactory $response,
        private UrlGenerator $url,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(
        CreateLabelRequest $request,
    ): JsonResponse {
        $request->save($this->label);

        return $this->response->json(
            data: new LabelAdminResource($this->label),
            status: 201,
        )
            ->header('Location', $this->url->to(
                '/labels/'.$this->label->id
            ));
    }
}
