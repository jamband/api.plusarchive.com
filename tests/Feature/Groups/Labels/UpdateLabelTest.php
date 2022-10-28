<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Labels;

use App\Groups\Countries\CountryFactory;
use App\Groups\Labels\Label;
use App\Groups\Labels\LabelFactory;
use App\Groups\LabelTags\LabelTag;
use App\Groups\LabelTags\LabelTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class UpdateLabelTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('PUT /labels/1');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('PUT /labels/1');
    }

    public function testModelNotFound(): void
    {
        $country = CountryFactory::new()
            ->createOne();

        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/labels/1', [
                'name' => 'label1',
                'country'=> $country->name,
                'url' => 'https://url1.dev',
            ])
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testUpdateLabel(): void
    {
        $label = LabelFactory::new()
            ->createOne();

        $this->assertDatabaseCount(Label::class, 1);

        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/labels/'.$label->id, [
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

        $this->assertDatabaseCount(Label::class, 1);

        $this->assertDatabaseHas(Label::class, [
            'id' => $label->id,
            'name' => 'updated_label1',
            'country_id' => 1,
            'url' => 'https://updated-url1.dev',
            'links' => "https://updated-link1.dev\nhttps://updated-link2.dev",
        ]);
    }

    public function testUpdateLabelWithSomeEmptyAttributeValues(): void
    {
        $label = LabelFactory::new()
            ->createOne();

        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/labels/'.$label->id, [
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

        $this->assertDatabaseHas(Label::class, [
            'id' => $label->id,
            'name' => 'updated_label1',
            'country_id' => 1,
            'url' => 'https://updated-url1.dev',
            'links' => '',
        ]);
    }

    public function testUpdateLabelWithTags(): void
    {
        $label = LabelFactory::new()
            ->createOne();

        $pivotTable = $label->tags()->getTable();

        LabelTagFactory::new()
            ->count(4)
            ->state(new Sequence(
                ['name' => 'tag1'],
                ['name' => 'tag2'],
                ['name' => 'tag3'],
                ['name' => 'tag4'],
            ))
            ->create();

        $label->tags()->sync([1, 2]);

        $this->assertDatabaseCount(LabelTag::class, 4);

        $this->assertDatabaseCount($pivotTable, 2);
        $this->assertDatabaseHas($pivotTable, ['label_id' => 1, 'tag_id' => 1]);
        $this->assertDatabaseHas($pivotTable, ['label_id' => 1, 'tag_id' => 2]);

        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/labels/'.$label->id, [
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

        $this->assertDatabaseCount(LabelTag::class, 4);

        $this->assertDatabaseCount($pivotTable, 2);
        $this->assertDatabaseHas($pivotTable, ['label_id' => 1, 'tag_id' => 3]);
        $this->assertDatabaseHas($pivotTable, ['label_id' => 1, 'tag_id' => 4]);
    }
}
