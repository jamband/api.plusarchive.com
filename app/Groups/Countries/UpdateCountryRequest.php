<?php

declare(strict_types=1);

namespace App\Groups\Countries;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string|null $name
 */
class UpdateCountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(Country $country): array
    {
        return [
            'name' => [
                'bail',
                'required',
                'string',
                'max:100',
                Rule::unique($country::class)->ignore($this->route('id')),
            ],
        ];
    }

    public function save(Country $country): void
    {
        $data = $this->validated();

        $country->name = $data['name'];
        $country->save();
    }
}
