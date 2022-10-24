<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Rules\RippleUrlRule;
use Jamband\Ripple\Ripple;
use Tests\TestCase;

class RippleUrlRuleTest extends TestCase
{
    private Ripple $ripple;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ripple = new Ripple();
    }

    public function testFails(): void
    {
        $this->ripple->request('https://example.com/foo/bar');

        $rule = new RippleUrlRule($this->ripple);
        $this->assertFalse($rule->passes('', null));
    }

    public function testPasses(): void
    {
        $this->ripple->request('https://soundcloud.com/foo/bar');

        $rule = new RippleUrlRule($this->ripple);
        $this->assertTrue($rule->passes('', null));
    }

    public function testMessage(): void
    {
        $this->ripple->request('https://example.com/foo/bar');

        $rule = new RippleUrlRule($this->ripple);
        $this->assertSame(__('validation.ripple.url'), $rule->message());
    }
}
