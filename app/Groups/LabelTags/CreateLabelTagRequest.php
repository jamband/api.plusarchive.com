<?php

declare(strict_types=1);

namespace App\Groups\LabelTags;

use App\Rules\TaggableRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string|null $name
 */
class CreateLabelTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(LabelTag $tag): array
    {
        return [
            'name' => [
                'bail',
                'required',
                'string',
                new TaggableRule(),
                Rule::unique($tag::class),
            ],
        ];
    }

    public function save(LabelTag $tag): void
    {
        $data = $this->validated();

        $tag->name = $data['name'];
        $tag->save();
    }
}
