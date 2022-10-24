<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetLabel extends Controller
{
    public function __construct(
        private Label $label,
        private ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(int $id): JsonResponse
    {
        return $this->response->json(
            data: new LabelAdminResource(
                $this->label::query()
                    ->with('country')
                    ->with('tags')
                    ->findOrFail($id)
            ),
        );
    }
}
