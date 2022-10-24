<?php

declare(strict_types=1);

namespace Tests\Feature\Exceptions;

use Tests\TestCase;

class HandlerTest extends TestCase
{
    public function testNotFound(): void
    {
        $this->getJson('/')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }
}
