<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Site;

use Carbon\Carbon;
use Tests\TestCase;

class GetCsrfCookieTest extends TestCase
{
    private string $token;
    private string $session;
    private Carbon $carbon;

    protected function setUp(): void
    {
        parent::setUp();

        $this->carbon = new Carbon();

        [$token, $session] = $this->get('/csrf-cookie')
            ->headers->all('set-cookie');

        assert(is_string($token));
        assert(is_string($session));

        $this->token = $token;
        $this->session = $session;
    }

    public function testCsrfCookie(): void
    {
        $attributes = explode('; ', $this->token);
        $this->assertCount(5, $attributes);

        $this->assertMatchesRegularExpression('/\AXSRF-TOKEN=eyJpdiI.+\z/', $this->token);
        $this->assertContains('expires='.$this->expires(), $attributes);
        $this->assertContains('Max-Age=7200', $attributes);
        $this->assertContains('path=/', $attributes);
        $this->assertContains('samesite=lax', $attributes);
    }

    public function testSessionCookie(): void
    {
        $attributes = explode('; ', $this->session);
        $this->assertCount(6, $attributes);

        $this->assertMatchesRegularExpression(
            '/\A'.str_replace('.', '', strtolower($this->app['config']['app.name'])).'_session=eyJpdiI.+\z/',
            $this->session
        );

        $this->assertContains('expires='.$this->expires(), $attributes);
        $this->assertContains('Max-Age=7200', $attributes);
        $this->assertContains('path=/', $attributes);
        $this->assertContains('httponly', $attributes);
        $this->assertContains('samesite=lax', $attributes);
    }

    private function expires(): string
    {
        return $this->carbon::now()
            ->addMinutes($this->app['config']['session.lifetime'])
            ->format('D, d M Y H:i:s \G\M\T');
    }
}
