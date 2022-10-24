<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Rules\TaggableRule;
use Tests\TestCase;

class TaggableRuleTest extends TestCase
{
    public function testFails(): void
    {
        $rule = new TaggableRule();

        $this->assertFalse($rule->passes('', null));
        $this->assertFalse($rule->passes('', true));
        $this->assertFalse($rule->passes('', false));
        $this->assertFalse($rule->passes('', 123));
        $this->assertFalse($rule->passes('', ['foo']));
        $this->assertFalse($rule->passes('', 'foo!'));
        $this->assertFalse($rule->passes('', str_repeat('a', 31)));
    }

    public function testPasses(): void
    {
        $rule = new TaggableRule();

        $this->assertTrue($rule->passes('', 'foo'));
        $this->assertTrue($rule->passes('', 'foo bar'));
        $this->assertTrue($rule->passes('', 'foo-bar'));
        $this->assertTrue($rule->passes('', 'foo_bar'));
    }

    public function testMessage(): void
    {
        $rule = new TaggableRule();
        $this->assertSame(__('validation.taggable'), $rule->message());
    }
}
