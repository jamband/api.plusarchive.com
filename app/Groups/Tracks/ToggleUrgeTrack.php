<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use Hashids\Hashids;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ToggleUrgeTrack extends Controller
{
    public function __construct(
        private readonly Hashids $hashids,
        private readonly Track $track,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(string $hash): JsonResponse
    {
        $id = $this->hashids->decode($hash);
        $id = empty($id) ? 0 : $id[0];
        assert(is_int($id));

        if ($this->track->toggleUrge($this->track->findOrFail($id))) {
            return $this->response->json(
                status: 204,
            );
        }

        throw new BadRequestHttpException('Can\'t urge more.');
    }
}
