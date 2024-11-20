<?php

declare(strict_types=1);

namespace Tests\Unit\Groups\Tracks;

use App\Groups\Tracks\TrackImage;
use Illuminate\Http\Client\Factory as Client;
use Tests\TestCase;

class TrackImageTest extends TestCase
{
    private TrackImage $trackImage;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var Client $client */
        $client = $this->app->make(Client::class);
        $client->fake();
        $this->instance(Client::class, $client);

        $this->trackImage = $this->app->make(TrackImage::class);
    }

    public function testWhenProviderIsBandcamp(): void
    {
        $this->trackImage->request('https://example.com/foo_8.jpg', 'Bandcamp');
        $this->assertSame('https://example.com/foo_4.jpg', $this->trackImage->toSmall());
    }

    public function testWhenProviderIsSoundCloud(): void
    {
        $this->trackImage->request('https://example.com/foo-t500x500.jpg', 'SoundCloud');
        $this->assertSame('https://example.com/foo-t300x300.jpg', $this->trackImage->toSmall());
    }

    public function testWhenProviderIsVimeo(): void
    {
        $this->trackImage->request('https://example.com/foo_640', 'Vimeo');
        $this->assertSame('https://example.com/foo_320', $this->trackImage->toSmall());
    }

    public function testWhenProviderIsYouTube(): void
    {
        $this->trackImage->request('https://example.com/foo/hqdefault.jpg', 'YouTube');
        $this->assertSame('https://example.com/foo/hqdefault.jpg', $this->trackImage->toSmall());
    }

    public function testWhenProviderIsUnknown(): void
    {
        $this->trackImage->request('https://example.com/foo.jpg', 'Foo');
        $this->assertSame('https://example.com/foo.jpg', $this->trackImage->toSmall());
    }
}
