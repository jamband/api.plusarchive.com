<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use App\Groups\Countries\Country;
use App\Groups\StoreTags\StoreTag;
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
class CreateStoreRequest extends FormRequest
{
    private Country $_country;
    private StoreTag $tag;
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
        Store $store,
        StoreTag $tag,
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
                Rule::unique($store::class),
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

    public function save(Store $store): void
    {
        $this->db->transaction(function () use ($store) {
            $data = $this->validated();
            $store->name = $data['name'];

            $countryId = $this->_country->getIdByName($data['country']);

            if (null !== $countryId) {
                $store->country_id = $countryId;
            }

            $store->url = $data['url'];
            $store->links = $data['links'] ?? '';
            $store->save();

            if (isset($data['tags'])) {
                $tagIds = $this->tag->getIdsByNames($data['tags']);
                $store->tags()->sync($tagIds);
            }
        });
    }
}
