<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Routing\UrlGenerator;

class Authenticate extends Middleware
{
    public function __construct(
        Auth $auth,
        private readonly UrlGenerator $url,
    ) {
        parent::__construct($auth);
    }

    protected function redirectTo($request): string
    {
        return $this->url->to('/');
    }
}
