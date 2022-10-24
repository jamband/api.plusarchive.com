<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Rules\TaggablesRule;
use Tests\TestCase;

class TaggablesRuleTest extends TestCase
{
    public function testFails(): void
    {
        $rule = new TaggablesRule();

        $this->assertFalse($rule->passes('', null));
        $this->assertFalse($rule->passes('', 'foo'));
        $this->assertFalse($rule->passes('', [null, 'foo']));
        $this->assertFalse($rule->passes('', ['foo', true]));
        $this->assertFalse($rule->passes('', [123, 'foo']));
        $this->assertFalse($rule->passes('', ['foo', 'bar!']));
        $this->assertFalse($rule->passes('', ['a']));
        $this->assertFalse($rule->passes('', [str_repeat('a', 31)]));
    }

    public function testPasses(): void
    {
        $rule = new TaggablesRule();

        $this->assertTrue($rule->passes('', []));
        $this->assertTrue($rule->passes('', ['foo', 'bar', 'baz']));
        $this->assertTrue($rule->passes('', ['foo-bar', 'foo_bar', 'foo bar']));
    }

    public function testMessage(): void
    {
        $rule = new TaggablesRule();
        $this->assertSame(__('validation.taggables'), $rule->message());
    }
}
