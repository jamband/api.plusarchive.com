<?php

declare(strict_types=1);

namespace App\Groups\TrackGenres;

use App\Rules\TaggableRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string|null $name
 */
class CreateTrackGenreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(TrackGenre $genre): array
    {
        return [
            'name' => [
                'bail',
                'required',
                'string',
                new TaggableRule(),
                Rule::unique($genre::class),
            ],
        ];
    }

    public function save(TrackGenre $genre): void
    {
        $data = $this->validated();

        $genre->name = $data['name'];
        $genre->save();
    }
}
