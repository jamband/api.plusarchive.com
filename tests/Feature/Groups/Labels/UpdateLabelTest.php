<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Labels;

use App\Groups\Countries\CountryFactory;
use App\Groups\Labels\LabelFactory;
use App\Groups\LabelTags\LabelTag;
use App\Groups\LabelTags\LabelTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UpdateLabelTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private CountryFactory $countryFactory;
    private LabelFactory $labelFactory;
    private LabelTagFactory $tagFactory;
    private LabelTag $labelTag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->countryFactory = new CountryFactory();
        $this->labelFactory = new LabelFactory();
        $this->tagFactory = new LabelTagFactory();
        $this->tag = New LabelTag();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->put('/labels/1')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->put('/labels/1')
            ->assertUnauthorized();
    }

    public function testModelNotFound(): void
    {
        $country = $this->countryFactory
            ->createOne();

        $this->actingAs($this->userFactory->makeOne())
            ->put('/labels/1', [
                'name' => 'label1',
                'country' => $country->name,
                'url' => 'https://url1.dev',
            ])
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testUpdateLabel(): void
    {
        $label = $this->labelFactory
            ->createOne();

        $this->assertDatabaseCount($label::class, 1);

        $this->actingAs($this->userFactory->makeOne())
            ->put('/labels/'.$label->id, [
                'name' => 'updated_label1',
                'country' => $label->country->name,
                'url' => 'https://updated-url1.dev',
                'links' => "https://updated-link1.dev\nhttps://updated-link2.dev",
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($label) {
                $json->where('id', $label->id)
                    ->where('name', 'updated_label1')
                    ->where('country', $label->country->name)
                    ->where('url', 'https://updated-url1.dev')
                    ->where('links', "https://updated-link1.dev\nhttps://updated-link2.dev")
                    ->where('tags', [])
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseCount($label::class, 1)
            ->assertDatabaseHas($label::class, [
                'id' => $label->id,
                'name' => 'updated_label1',
                'country_id' => 1,
                'url' => 'https://updated-url1.dev',
                'links' => "https://updated-link1.dev\nhttps://updated-link2.dev",
            ]);
    }

    public function testUpdateLabelWithSomeEmptyAttributeValues(): void
    {
        $label = $this->labelFactory
            ->createOne();

        $this->actingAs($this->userFactory->makeOne())
            ->put('/labels/'.$label->id, [
                'name' => 'updated_label1',
                'country' => $label->country->name,
                'url' => 'https://updated-url1.dev',
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($label) {
                $json->where('id', $label->id)
                    ->where('name', 'updated_label1')
                    ->where('country', $label->country->name)
                    ->where('url', 'https://updated-url1.dev')
                    ->where('links', '')
                    ->where('tags', [])
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseHas($label::class, [
            'id' => $label->id,
            'name' => 'updated_label1',
            'country_id' => 1,
            'url' => 'https://updated-url1.dev',
            'links' => '',
        ]);
    }

    public function testUpdateLabelWithTags(): void
    {
        $label = $this->labelFactory
            ->createOne();

        $pivotTable = $label->tags()->getTable();

        $this->tagFactory
            ->count(4)
            ->state(new Sequence(
                ['name' => 'tag1'],
                ['name' => 'tag2'],
                ['name' => 'tag3'],
                ['name' => 'tag4'],
            ))
            ->create();

        $label->tags()->sync([1, 2]);

        $this->assertDatabaseCount($this->tag::class, 4)
            ->assertDatabaseCount($pivotTable, 2)
            ->assertDatabaseHas($pivotTable, ['label_id' => 1, 'tag_id' => 1])
            ->assertDatabaseHas($pivotTable, ['label_id' => 1, 'tag_id' => 2]);

        $this->actingAs($this->userFactory->makeOne())
            ->put('/labels/'.$label->id, [
                'name' => 'updated_label1',
                'country' => $label->country->name,
                'url' => 'https://updated-url1.dev',
                'tags' => ['tag3', 'tag4'],
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($label) {
                $json->where('id', $label->id)
                    ->where('name', 'updated_label1')
                    ->where('country', $label->country->name)
                    ->where('url', 'https://updated-url1.dev')
                    ->where('links', '')
                    ->where('tags', ['tag3', 'tag4'])
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseCount($this->tag::class, 4)
            ->assertDatabaseCount($pivotTable, 2)
            ->assertDatabaseHas($pivotTable, ['label_id' => 1, 'tag_id' => 3])
            ->assertDatabaseHas($pivotTable, ['label_id' => 1, 'tag_id' => 4]);
    }
}
