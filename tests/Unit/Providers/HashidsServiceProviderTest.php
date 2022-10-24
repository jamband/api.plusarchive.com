<?php

declare(strict_types=1);

namespace Tests\Unit\Providers;

use Hashids\Hashids;
use Tests\TestCase;

class HashidsServiceProviderTest extends TestCase
{
    private Hashids $hashids;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hashids = $this->app->make(Hashids::class);
    }

    public function testEncode(): void
    {
        $original = new Hashids('test', 11, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-');
        $this->assertSame($original->encode(123), $this->hashids->encode(123));
    }

    public function testDecode(): void
    {
        $hashId = $this->hashids->encode(123);
        $this->assertSame(123, $this->hashids->decode($hashId)[0]);
    }
}
