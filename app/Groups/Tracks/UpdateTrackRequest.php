<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use App\Groups\MusicProviders\MusicProvider;
use App\Groups\TrackGenres\TrackGenre;
use App\Rules\RippleImageRule;
use App\Rules\RippleUrlRule;
use App\Rules\TaggablesRule;
use Hashids\Hashids;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Jamband\Ripple\Ripple;

/**
 * @property string|null $url
 * @property int|null $provider_id
 * @property string|null $provider_key
 * @property string|null $title
 * @property string|null $image
 * @property array<int, string>|null $genres
 */
class UpdateTrackRequest extends FormRequest
{
    private DatabaseManager $db;
    private Ripple $ripple;
    private MusicProvider $provider;
    private TrackImage $trackImage;
    private TrackGenre $genre;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(
        DatabaseManager $db,
        Ripple $ripple,
        MusicProvider $provider,
        Track $track,
        TrackImage $trackImage,
        TrackGenre $genre,
        Hashids $hashids,
    ): array {
        $this->db = $db;
        $this->ripple = $ripple;
        $this->provider = $provider;
        $this->trackImage = $trackImage;
        $this->genre = $genre;

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
                Rule::unique($track::class)->ignore($id),
                new RippleUrlRule($this->ripple),
                new RippleImageRule($this->ripple),
            ],
            'title' => [
                'bail',
                'nullable',
                'string',
                'max:200',
            ],
            'image' => [
                'bail',
                'nullable',
                'url',
            ],
            'genres' => [
                'bail',
                'nullable',
                new TaggablesRule(),
            ],
        ];
    }

    public function save(Track $track): void
    {
        $this->db->transaction(function () use ($track) {
            $data = $this->validated();

            $track->url = $data['url'];

            $provider = $this->ripple->provider();
            assert(is_string($provider));

            $providerId = $this->provider->getIdByName($provider);
            assert(is_int($providerId));

            $track->provider_id = $providerId;

            $providerKey = $this->ripple->id();
            assert(is_string($providerKey));

            $track->provider_key = $providerKey;

            $track->title = $data['title'] ?? $this->ripple->title();

            if (isset($data['image'])) {
                $image = $data['image'];
            } else {
                $image = $this->ripple->image();
                assert(is_string($image));

                $this->trackImage->request($image, $provider);
                $image = $this->trackImage->toSmall();
            }

            $track->image = $image;
            $track->urge = false;
            $track->save();

            if (isset($data['genres'])) {
                $genreIds = $this->genre->getIdsByNames($data['genres']);
                $track->genres()->sync($genreIds);
            }
        });
    }
}
