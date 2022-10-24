<?php

declare(strict_types=1);

namespace Tests\Feature\Labels;

use App\Groups\Countries\CountryFactory;
use App\Groups\Labels\Label;
use App\Groups\LabelTags\LabelTag;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class CreateLabelTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('POST /labels');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('POST /labels');
    }

    public function testCreateLabel(): void
    {
        $country = CountryFactory::new()
            ->createOne();

        $this->assertDatabaseCount(Label::class, 0);

        $this->actingAs(UserFactory::new()->makeOne())
            ->postJson('/labels', [
                'name' => 'label1',
                'country' => $country->name,
                'url' => 'https://url1.dev',
                'links' => "https://link1.dev\nhttps://link2.dev",
            ])
            ->assertCreated()
            ->assertHeader('Location', $this->app['config']['app.url'].'/labels/1')
            ->assertJson(function (AssertableJson $json) use ($country) {
                $json->where('id', 1)
                    ->where('name', 'label1')
                    ->where('country', $country->name)
                    ->where('url', 'https://url1.dev')
                    ->where('links', "https://link1.dev\nhttps://link2.dev")
                    ->where('tags', [])
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseCount(Label::class, 1);

        $this->assertDataBaseHas(Label::class, [
            'id' => 1,
            'name' => 'label1',
            'country_id' => $country->id,
            'url' => 'https://url1.dev',
            'links' => "https://link1.dev\nhttps://link2.dev",
        ]);
    }

    public function testCreateLabelWithSomeEmptyAttributeValues(): void
    {
        $country = CountryFactory::new()
            ->createOne();

        $this->actingAs(UserFactory::new()->makeOne())
            ->postJson('/labels', [
                'name' => 'label1',
                'country' => $country->name,
                'url' => 'https://url1.dev',
            ])
            ->assertCreated()
            ->assertJson(function (AssertableJson $json) use ($country) {
                $json->where('id', 1)
                    ->where('name', 'label1')
                    ->where('country', $country->name)
                    ->where('url', 'https://url1.dev')
                    ->where('links', '')
                    ->where('tags', [])
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseHas(Label::class, [
            'id' => 1,
            'name' => 'label1',
            'country_id' => $country->id,
            'url' => 'https://url1.dev',
            'links' => '',
        ]);
    }

    public function testCreateLabelWithTags(): void
    {
        $label = new Label();
        $pivotTable = $label->tags()->getTable();

        $country = CountryFactory::new()
            ->createOne();

        $this->actingAs(UserFactory::new()->makeOne())
            ->postJson('/labels', [
                'name' => 'label1',
                'country' => $country->name,
                'url' => 'https://url1.dev',
                'tags' => ['tag1', 'tag2'],
            ])
            ->assertCreated()
            ->assertJson(function (AssertableJson $json) use ($country) {
                $json->where('id', 1)
                    ->where('name', 'label1')
                    ->where('country', $country->name)
                    ->where('url', 'https://url1.dev')
                    ->where('links', '')
                    ->where('tags', ['tag1', 'tag2'])
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseCount(LabelTag::class, 2);
        $this->assertDatabaseHas(LabelTag::class, ['name' => 'tag1']);
        $this->assertDatabaseHas(LabelTag::class, ['name' => 'tag2']);

        $this->assertDatabaseCount($pivotTable, 2);
        $this->assertDatabaseHas($pivotTable, ['label_id' => 1, 'tag_id' => 1]);
        $this->assertDatabaseHas($pivotTable, ['label_id' => 1, 'tag_id' => 2]);
    }
}
