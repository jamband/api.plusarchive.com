<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class DeleteStore extends Controller
{
    public function __construct(
        private readonly Store $store,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(int $id): Response
    {
        $this->store::query()
            ->findOrFail($id)
            ->delete();

        return $this->response->noContent();
    }
}
