<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

class CreateStore extends Controller
{
    public function __construct(
        private readonly Store $store,
        private readonly ResponseFactory $response,
        private readonly UrlGenerator $url,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(CreateStoreRequest $request): Response
    {
        $request->save($this->store);

        return $this->response->make(
            $this->store->toResource(StoreAdminResource::class),
            201,
        )
            ->header('Location', $this->url->to(
                '/stores/'.$this->store->id
            ));
    }
}
