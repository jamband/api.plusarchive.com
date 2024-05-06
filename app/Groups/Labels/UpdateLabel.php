<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class UpdateLabel extends Controller
{
    public function __construct(
        private readonly Label $label,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(UpdateLabelRequest $request, int $id): Response
    {
        $label = $this->label->findOrFail($id);
        assert($label instanceof Label);

        $request->save($label);

        return $this->response->make(
            new LabelAdminResource($label),
        );
    }
}
