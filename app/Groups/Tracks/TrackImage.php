<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use Illuminate\Http\Client\Factory as Client;

class TrackImage
{
    private string $url;
    private string $provider;

    public function __construct(
        private Client $client,
    ) {
    }

    public function request(string $url, string $provider): void
    {
        $this->url = $url;
        $this->provider = $provider;
    }

    public function toSmall(): string
    {
        $image = match ($this->provider) {
            'Bandcamp' => preg_replace('/[0-9]+\.jpg\z/', '4.jpg', $this->url),
            'SoundCloud' => str_replace('t500x500', 't300x300', $this->url),
            'Vimeo' => preg_replace('/_640\z/', '_320', $this->url),
            default => $this->url,
        };

        assert(is_string($image));

        return $this->client->get($image)->ok()
            ? $image
            : $this->url;
    }
}
