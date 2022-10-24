<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Rules\LimitableRule;
use Tests\TestCase;

class LimitableRuleTest extends TestCase
{
    public function testFails(): void
    {
        $rule = new LimitableRule(count: 2, limit: 1);
        $this->assertFalse($rule->passes('', true));
    }

    public function testPasses(): void
    {
        $rule = new LimitableRule(count: 1, limit: 2);
        $this->assertTrue($rule->passes('', true));

        $rule = new LimitableRule(count: 2, limit: 1);
        $this->assertTrue($rule->passes('', false));
    }

    public function testMessage(): void
    {
        $rule = new LimitableRule(6, 5);
        $this->assertSame(__('validation.limitable', ['limit' => 5]), $rule->message());
    }
}
