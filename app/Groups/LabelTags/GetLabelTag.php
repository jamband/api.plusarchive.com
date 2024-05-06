<?php

declare(strict_types=1);

namespace App\Groups\LabelTags;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetLabelTag extends Controller
{
    public function __construct(
        private readonly LabelTag $tag,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(int $id): Response
    {
        return $this->response->make(
            $this->tag::query()
                ->findOrFail($id),
        );
    }
}
