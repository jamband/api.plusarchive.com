<?php


declare(strict_types=1);

namespace Tests\Feature\Groups\Labels;

use App\Groups\Countries\CountryFactory;
use App\Groups\Labels\Label;
use App\Groups\LabelTags\LabelTag;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CreateLabelTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private CountryFactory $countryFactory;
    private Label $label;
    private LabelTag $tag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->countryFactory = new CountryFactory();
        $this->label = new Label();
        $this->tag = new LabelTag();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->post('/labels')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->post('/labels')
            ->assertUnauthorized();
    }

    public function testCreateLabel(): void
    {
        $country = $this->countryFactory
            ->createOne();

        $this->assertDatabaseCount($this->label::class, 0);

        $this->actingAs($this->userFactory->makeOne())
            ->post('/labels', [
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

        $this->assertDatabaseCount($this->label::class, 1)
            ->assertDataBaseHas($this->label::class, [
                'id' => 1,
                'name' => 'label1',
                'country_id' => $country->id,
                'url' => 'https://url1.dev',
                'links' => "https://link1.dev\nhttps://link2.dev",
            ]);
    }

    public function testCreateLabelWithSomeEmptyAttributeValues(): void
    {
        $country = $this->countryFactory
            ->createOne();

        $this->actingAs($this->userFactory->makeOne())
            ->post('/labels', [
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

        $this->assertDatabaseHas($this->label::class, [
            'id' => 1,
            'name' => 'label1',
            'country_id' => $country->id,
            'url' => 'https://url1.dev',
            'links' => '',
        ]);
    }

    public function testCreateLabelWithTags(): void
    {
        $pivotTable = $this->label->tags()->getTable();

        $country = $this->countryFactory
            ->createOne();

        $this->actingAs($this->userFactory->makeOne())
            ->post('/labels', [
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

        $this->assertDatabaseCount($this->tag::class, 2)
            ->assertDatabaseHas(LabelTag::class, ['name' => 'tag1'])
            ->assertDatabaseHas(LabelTag::class, ['name' => 'tag2'])
            ->assertDatabaseCount($pivotTable, 2)
            ->assertDatabaseHas($pivotTable, ['label_id' => 1, 'tag_id' => 1])
            ->assertDatabaseHas($pivotTable, ['label_id' => 1, 'tag_id' => 2]);
    }
}
