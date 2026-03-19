<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use Hashids\Hashids;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class ToggleUrge
{
    public function __construct(
        private Hashids $hashids,
        private Track $track,
        private ResponseFactory $response,
        private Application $app,
    ) {
    }

    public function __invoke(string $hash): Response
    {
        $id = $this->hashids->decode($hash);
        $id = empty($id) ? 0 : $id[0];

        if ($this->track->toggleUrge($this->track->findOrFail($id))) {
            return $this->response->noContent();
        }

        $this->app->abort(400, 'Can\'t urge more.');
    }
}
