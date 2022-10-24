<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Rules\MultipleUrlsRule;
use Tests\TestCase;

/**
 * Note that if the URL contains a path,
 * the validation will pass even if the delimiter is "," or "|".
 */
class MultipleUrlsRuleTest extends TestCase
{
    public function testFailsWithEmptyString(): void
    {
        $rule = new MultipleUrlsRule();
        $this->assertFalse($rule->passes('', ''));
    }

    public function testFailsWithoutHttpProtocol(): void
    {
        $rule = new MultipleUrlsRule();

        $value = implode("\n", [
            'example.com/foo',
            'https://example.com/bar',
            'https://example.com/baz',
        ]);

        $this->assertFalse($rule->passes('', $value));
    }

    public function testFailsWithSshProtocol(): void
    {
        $rule = new MultipleUrlsRule();

        $value = implode("\n", [
            'https://example.com/foo',
            'ssh://git@example.com:user/bar.git',
            'https://example.com/baz',
        ]);

        $this->assertFalse($rule->passes('', $value));
    }

    public function testFailsWithMailToProtocol(): void
    {
        $rule = new MultipleUrlsRule();

        $value = implode("\n", [
            'https://example.com/foo',
            'https://example.com/bar',
            'mailto:baz@example.com',
        ]);

        $this->assertFalse($rule->passes('', $value));
    }

    public function testFailsWithInvalidSeparator(): void
    {
        $rule = new MultipleUrlsRule();

        $value = implode(' ', [
            'https://example.com/foo',
            'https://example.com/bar',
            'https://example.com/baz',
        ]);

        $this->assertFalse($rule->passes('', $value));
    }

    public function testPasses(): void
    {
        $rule = new MultipleUrlsRule();

        $value = implode("\n", [
            'https://example.com/foo',
            'https://example.com/bar',
            'https://example.com/baz',
        ]);

        $this->assertTrue($rule->passes('', $value));
    }

    public function testMessage(): void
    {
        $rule = new MultipleUrlsRule();
        $this->assertSame(__('validation.multiple_urls'), $rule->message());
    }
}
