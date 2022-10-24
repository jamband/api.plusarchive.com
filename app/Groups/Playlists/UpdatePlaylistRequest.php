<?php

declare(strict_types=1);

namespace App\Groups\Playlists;

use App\Groups\MusicProviders\MusicProvider;
use App\Rules\RippleImageRule;
use App\Rules\RippleUrlRule;
use Hashids\Hashids;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Jamband\Ripple\Ripple;

/**
 * @property string|null $url
 * @property int|null $provider_id
 * @property string|null $provider_key
 * @property string|null $title
 */
class UpdatePlaylistRequest extends FormRequest
{
    private Ripple $ripple;
    private MusicProvider $provider;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(
        Ripple $ripple,
        MusicProvider $provider,
        Playlist $playlist,
        Hashids $hashids,
    ): array {
        $this->ripple = $ripple;
        $this->provider = $provider;

        if (is_string($this->url)) {
            $this->ripple->request($this->url);
        }

        $hash = $this->route('hash');
        assert(is_string($hash));

        $id = $hashids->decode($hash);
        $id = empty($id) ? 0 : $id[0];

        return [
            'url' => [
                'bail',
                'required',
                'string',
                Rule::unique($playlist::class)->ignore($id),
                new RippleUrlRule($this->ripple),
                new RippleImageRule($this->ripple),
            ],
            'title' => [
                'bail',
                'nullable',
                'string',
                'max:200',
            ],
        ];
    }

    public function save(Playlist $playlist): void
    {
        $data = $this->validated();

        $playlist->url = $data['url'];

        $provider = $this->ripple->provider();
        assert(is_string($provider));

        $providerId = $this->provider->getIdByName($provider);
        assert(is_int($providerId));
        $this->provider_id = $providerId;

        $providerKey = $this->ripple->id();
        assert(is_string($providerKey));

        $playlist->provider_key = $providerKey;
        $playlist->title = $data['title'] ?? $this->ripple->title();
        $playlist->save();
    }
}
