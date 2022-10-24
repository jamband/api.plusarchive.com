<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Rules\RippleImageRule;
use Jamband\Ripple\Ripple;
use Tests\TestCase;

class RippleImageRuleTest extends TestCase
{
    private Ripple $ripple;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ripple = new Ripple();
    }

    public function testFails(): void
    {
        $this->ripple->options(['response' => '']);
        $this->ripple->request('https://soundcloud.com/foo/bar');

        $rule = new RippleImageRule($this->ripple);
        $this->assertFalse($rule->passes('', null));
    }

    public function testPasses(): void
    {
        $this->ripple->options(['response' => '<meta property="og:image" content="image">']);
        $this->ripple->request('https://soundcloud.com/foo/bar');

        $rule = new RippleImageRule($this->ripple);
        $this->assertTrue($rule->passes('', null));
    }

    public function testMessage(): void
    {
        $this->ripple->options(['response' => '']);
        $this->ripple->request('https://soundcloud.com/foo/bar');

        $rule = new RippleImageRule($this->ripple);
        $this->assertSame(__('validation.ripple.image'), $rule->message());
    }
}
