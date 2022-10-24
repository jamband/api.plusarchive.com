<?php

declare(strict_types=1);

namespace App\Groups\MusicProviders;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string|null $name
 */
class UpdateMusicProviderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(MusicProvider $provider): array
    {
        return [
            'name' => [
                'bail',
                'required',
                'string',
                'max:100',
                Rule::unique($provider::class)->ignore($this->route('id')),
            ],
        ];
    }

    public function save(MusicProvider $provider): void
    {
        $data = $this->validated();

        $provider->name = $data['name'];
        $provider->save();
    }
}
