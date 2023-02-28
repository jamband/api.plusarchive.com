<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Rules\TaggableRule;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Validation\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TaggableRuleTest extends TestCase
{
    private Translator $translator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->translator = $this->app->make(Translator::class);
    }

    #[DataProvider('providerFails')]
    public function testFails(mixed $input): void
    {
        $validator = new Validator(
            $this->translator,
            ['foo' => $input],
            ['foo' => new TaggableRule()]
        );

        $this->assertTrue($validator->fails());
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    public static function providerFails(): array
    {
        return [
            [null],
            [true],
            [false],
            [123],
            [['foo']],
            ['foo!'],
            [str_repeat('a', 31)],
        ];
    }

    #[DataProvider('providerPasses')]
    public function testPasses(string $input): void
    {
        $validator = new Validator(
            $this->translator,
            ['foo' => $input],
            ['foo' => new TaggableRule()]
        );

        $this->assertTrue($validator->passes());
    }

    /**
     * @return array<int, array<int, string>>
     */
    public static function providerPasses(): array
    {
        return [
            ['foo'],
            ['foo bar'],
            ['foo-bar'],
            ['foo_bar'],
        ];
    }

    public function testMessage(): void
    {
        $validator = new Validator(
            $this->translator,
            ['foo' => null],
            ['foo' => new TaggableRule()]
        );

        $this->assertSame(
            __('validation.taggable', ['attribute' => 'foo']),
            $validator->messages()->first()
        );
    }
}
