<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use Illuminate\Http\Response;
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

    public function __invoke(int $id): Response
    {
        $this->label::query()
            ->findOrFail($id)
            ->delete();

        return $this->response->noContent();
    }
}
