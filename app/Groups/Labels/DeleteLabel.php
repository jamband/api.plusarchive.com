<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class DeleteLabel extends Controller
{
    public function __construct(
        private readonly Label $label,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(int $id): JsonResponse
    {
        $this->label::query()
            ->findOrFail($id)
            ->delete();

        return $this->response->json(
            status: 204,
        );
    }
}
