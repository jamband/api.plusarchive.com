<?php

declare(strict_types=1);

namespace App\Groups\Site;

use Illuminate\Routing\RouteRegistrar;

/** @var RouteRegistrar $router */
$router->get('/csrf-cookie', GetCsrfCookie::class);
