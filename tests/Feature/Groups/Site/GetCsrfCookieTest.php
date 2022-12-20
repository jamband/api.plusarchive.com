<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Site;

use Carbon\Carbon;
use Tests\TestCase;

class GetCsrfCookieTest extends TestCase
{
    public function testCsrfCookie(): void
    {
        $response = $this->getJson('/csrf-cookie');
        $setCookie = $response->headers->all('set-cookie');
        $this->assertCount(2, $setCookie);

        [$token, ] = $setCookie;
        assert(is_string($token));
        $tokenValues = explode('; ', $token);
        $this->assertCount(5, $tokenValues);

        $this->assertMatchesRegularExpression('/\AXSRF-TOKEN=eyJpdiI.+\z/', $token);
        $this->assertContains('expires='.$this->expires(), $tokenValues);
        $this->assertContains('Max-Age=7200', $tokenValues);
        $this->assertContains('path=/', $tokenValues);
        $this->assertContains('samesite=lax', $tokenValues);
    }

    public function testSessionCookie(): void
    {
        $response = $this->getJson('/csrf-cookie');
        $setCookie = $response->headers->all('set-cookie');
        $this->assertCount(2, $setCookie);

        [, $session] = $setCookie;
        assert(is_string($session));
        $sessionValues = explode('; ', $session);
        $this->assertCount(6, $sessionValues);

        $this->assertMatchesRegularExpression(
            '/\A'.str_replace('.', '', strtolower($this->app['config']['app.name'])).'_session=eyJpdiI.+\z/',
            $session
        );

        $this->assertContains('expires='.$this->expires(), $sessionValues);
        $this->assertContains('Max-Age=7200', $sessionValues);
        $this->assertContains('path=/', $sessionValues);
        $this->assertContains('httponly', $sessionValues);
        $this->assertContains('samesite=lax', $sessionValues);
    }

    private function expires(): string
    {
        return (new Carbon())
            ->addMinutes($this->app['config']['session.lifetime'])
            ->format('D, d M Y H:i:s').' GMT';
    }
}
