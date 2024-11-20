<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use Hashids\Hashids;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class ToggleUrge extends Controller
{
    public function __construct(
        private readonly Hashids $hashids,
        private readonly Track $track,
        private readonly ResponseFactory $response,
        private readonly Application $app,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
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
