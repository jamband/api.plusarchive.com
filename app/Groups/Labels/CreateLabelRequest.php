<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use App\Groups\Countries\Country;
use App\Groups\LabelTags\LabelTag;
use App\Rules\MultipleUrlsRule;
use App\Rules\TaggablesRule;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string|null $name
 * @property string|null $country
 * @property string|null $url
 * @property string|null $links
 * @property array<int, string>|null $tags
 */
class CreateLabelRequest extends FormRequest
{
    private Country $_country;
    private LabelTag $tag;
    private DatabaseManager $db;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(
        Country $country,
        Label $label,
        LabelTag $tag,
        DatabaseManager $db,
    ): array {
        $this->_country = $country;
        $this->tag = $tag;
        $this->db = $db;

        return [
            'name' => [
                'bail',
                'required',
                'string',
                'max:200',
            ],
            'country' => [
                'bail',
                'required',
                Rule::exists($this->_country::class, 'name'),
            ],
            'url' => [
                'bail',
                'required',
                'string',
                'url',
                Rule::unique($label::class),
            ],
            'links' => [
                'bail',
                'nullable',
                'string',
                'max:1000',
                new MultipleUrlsRule(),
            ],
            'tags' => [
                'bail',
                'nullable',
                new TaggablesRule(),
            ],
        ];
    }

    public function save(Label $label): void
    {
        $this->db->transaction(function () use ($label) {
            $data = $this->validated();
            $label->name = $data['name'];

            $countryId = $this->_country->getIdByName($data['country']);

            if (null !== $countryId) {
                $label->country_id = $countryId;
            }

            $label->url = $data['url'];
            $label->links = $data['links'] ?? '';
            $label->save();

            if (isset($data['tags'])) {
                $tagIds = $this->tag->getIdsByNames($data['tags']);
                $label->tags()->sync($tagIds);
            }
        });
    }
}
