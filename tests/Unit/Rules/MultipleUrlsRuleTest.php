<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Rules\MultipleUrlsRule;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Validation\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class MultipleUrlsRuleTest extends TestCase
{
    private Translator $translator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->translator = $this->app->make(Translator::class);
    }

    #[DataProvider('providerFails')]
    public function testFailsWithoutHttpProtocol(string $input): void
    {
        $validator = new Validator(
            $this->translator,
            ['foo' => $input],
            ['foo' => new MultipleUrlsRule()]
        );

        $this->assertTrue($validator->fails());
    }

    /**
     * @return array<int, array<int, string>>
     */
    public static function providerFails(): array
    {
        return [
            ["example.com/foo\nhttps://example.com/bar\nhttps://example.com/baz"],
            ["https://example.com/foo\nssh://git@example.com:user/bar.git\nhttps://example.com/baz"],
            ["https://example.com/foo\nhttps://example.com/bar\nmailto:baz@example.com"],
        ];
    }

    public function testPasses(): void
    {
        $validator = new Validator(
            $this->translator,
            ['foo' => "https://example.com/foo\nhttps://example.com/bar\nhttps://example.com/baz"],
            ['foo' => new MultipleUrlsRule()]
        );

        $this->assertTrue($validator->passes());
    }

    public function testMessage(): void
    {
        $validator = new Validator(
            $this->translator,
            ['foo' => 'bar'],
            ['foo' => new MultipleUrlsRule()]
        );

        $this->assertSame(
            __('validation.multiple_urls', ['attribute' => 'foo']),
            $validator->messages()->first()
        );
    }
}
