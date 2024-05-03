<?php

declare(strict_types=1);

namespace Tests\Unit\Groups\Labels;

use App\Groups\Countries\CountryFactory;
use App\Groups\Labels\Label;
use App\Groups\Labels\LabelFactory;
use App\Groups\LabelTags\LabelTagFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LabelScopeTest extends TestCase
{
    use RefreshDatabase;

    private Label $label;
    private LabelFactory $labelFactory;
    private LabelTagFactory $tagFactory;
    private CountryFactory $countryFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label = new Label();
        $this->labelFactory = new LabelFactory();
        $this->tagFactory = new LabelTagFactory();
        $this->countryFactory = new CountryFactory();
    }

    public function testScopeOfCountry(): void
    {
        $this->labelFactory
            ->count(1)
            ->for(
                $this->countryFactory
                    ->state(['name' => 'foo'])
            )
            ->create();

        $this->labelFactory
            ->count(2)
            ->for(
                $this->countryFactory
                    ->state(['name' => 'bar'])
            )
            ->create();

        $this->assertSame(0, $this->label->ofCountry('')->count());
        $this->assertSame(1, $this->label->ofCountry('foo')->count());
        $this->assertSame(2, $this->label->ofCountry('bar')->count());
        $this->assertSame(0, $this->label->ofCountry('baz')->count());
    }

    public function testScopeOfTag(): void
    {
        /** @var array<int, Label> $labels */
        $labels = $this->labelFactory
            ->count(2)
            ->create();

        $this->tagFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $labels[0]->tags()->sync([1, 2]);
        $labels[1]->tags()->sync([2]);

        $this->assertSame(0, $this->label->ofTag('')->count());
        $this->assertSame(1, $this->label->ofTag('foo')->count());
        $this->assertSame(2, $this->label->ofTag('bar')->count());
        $this->assertSame(0, $this->label->ofTag('baz')->count());
    }

    public function testScopeOfSearch(): void
    {
        $this->labelFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->assertSame(0, $this->label->ofSearch('')->count());
        $this->assertSame(1, $this->label->ofSearch('o')->count());
        $this->assertSame(2, $this->label->ofSearch('ba')->count());
        $this->assertSame(0, $this->label->ofSearch('qux')->count());
    }

    public function testScopeInNameOrder(): void
    {
        $this->labelFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->assertSame(
            ['bar', 'baz', 'foo'],
            $this->label->inNameOrder()->pluck('name')->toArray()
        );
    }
}
