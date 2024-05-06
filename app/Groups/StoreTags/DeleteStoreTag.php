<?php

declare(strict_types=1);

namespace App\Groups\StoreTags;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class DeleteStoreTag extends Controller
{
    public function __construct(
        private readonly StoreTag $tag,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(int $id): Response
    {
        $this->tag::query()
            ->findOrFail($id)
            ->delete();

        return $this->response->noContent();
    }
}
