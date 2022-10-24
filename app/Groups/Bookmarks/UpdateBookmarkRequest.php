<?php

declare(strict_types=1);

namespace App\Groups\Bookmarks;

use App\Groups\BookmarkTags\BookmarkTag;
use App\Groups\Countries\Country;
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
class UpdateBookmarkRequest extends FormRequest
{
    private Country $_country;
    private BookmarkTag $tag;
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
        Bookmark $bookmark,
        BookmarkTag $tag,
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
                Rule::unique($bookmark::class)->ignore($this->route('id')),
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

    public function save(Bookmark $bookmark): void
    {
        $this->db->transaction(function () use ($bookmark) {
            $data = $this->validated();

            $bookmark->name = $data['name'];

            $countryId = $this->_country->getIdByName($data['country']);

            if (null !== $countryId) {
                $bookmark->country_id = $countryId;
            }

            $bookmark->url = $data['url'];
            $bookmark->links = $data['links'] ?? '';
            $bookmark->save();

            if (isset($data['tags'])) {
                $tagIds = $this->tag->getIdsByNames($data['tags']);
                $bookmark->tags()->sync($tagIds);
            }
        });
    }
}
