<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Rules\TaggablesRule;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Validation\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TaggablesRuleTest extends TestCase
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
            ['foo' => new TaggablesRule()]
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
            ['foo'],
            [null, 'foo'],
            [123, 'foo'],
            ['foo', 'bar!'],
            ['a'],
            [str_repeat('a', 31)],
        ];
    }

    /**
     * @param array<int, string>|array{} $input
     */
    #[DataProvider('providerPasses')]
    public function testPasses(array $input): void
    {
        $validator = new Validator(
            $this->translator,
            ['foo' => $input],
            ['foo' => new TaggablesRule()]
        );

        $this->assertTrue($validator->passes());
    }

    /**
     * @return array<int, array<int, array<int, string>|array{}>>
     */
    public static function providerPasses(): array
    {
        return [
            [[]],
            [['foo', 'bar', 'baz']],
            [['foo-bar', 'foo_bar', 'foo bar']],
        ];
    }

    public function testMessageWithoutArray(): void
    {
        $validator = new Validator(
            $this->translator,
            ['foo' => null],
            ['foo' => new TaggablesRule()]
        );

        $this->assertSame(
            __('validation.taggables.array', ['attribute' => 'foo']),
            $validator->messages()->first()
        );
    }

    public function testMessageWithInvalidTag(): void
    {
        $validator = new Validator(
            $this->translator,
            ['foo' => ['foo!']],
            ['foo' => new TaggablesRule()]
        );

        $this->assertSame(
            __('validation.taggables.tag', ['attribute' => 'foo']),
            $validator->messages()->first()
        );
    }
}
