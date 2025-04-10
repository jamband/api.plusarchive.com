<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetStore extends Controller
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
        return $this->response->make(
            $this->store::query()
                ->with('country')
                ->with('tags')
                ->findOrFail($id)
                ->toResource(StoreAdminResource::class),
        );
    }
}
