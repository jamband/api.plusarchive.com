<?php

declare(strict_types=1);

namespace Tests\Unit\Groups\Labels;

use App\Groups\Countries\CountryFactory;
use App\Groups\Labels\Label;
use App\Groups\Labels\LabelFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LabelTest extends TestCase
{
    use RefreshDatabase;

    private Label $label;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label = new Label();
    }

    public function testTimestamps(): void
    {
        $this->assertTrue($this->label->timestamps);
    }

    public function testCountry(): void
    {
        $relation = $this->label->country();
        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame('country_id', $relation->getForeignKeyName());
    }

    public function testTags(): void
    {
        $pivot = $this->label->tags();

        $this->assertInstanceOf(BelongsToMany::class, $pivot);
        $this->assertSame('tag_label', $pivot->getTable());
        $this->assertSame('labels', $pivot->getParent()->getTable());
        $this->assertSame('label_tags', $pivot->getRelated()->getTable());
        $this->assertSame('tag_id', $pivot->getRelatedPivotKeyName());
        $this->assertSame('label_id', $pivot->getForeignPivotKeyName());
    }

    public function testGetCountryNames(): void
    {
        CountryFactory::new()
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        LabelFactory::new()
            ->count(5)
            ->state(new Sequence(
                ['country_id' => 1],
                ['country_id' => 2],
                ['country_id' => 2],
                ['country_id' => 3],
                ['country_id' => 3],
            ))
            ->create();

        $this->assertSame(['bar', 'baz', 'foo'], $this->label->getCountryNames());
    }
}
