<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Rules\RippleImageRule;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Validation\Validator;
use Jamband\Ripple\Ripple;
use Tests\TestCase;

class RippleImageRuleTest extends TestCase
{
    private Ripple $ripple;
    private Translator $translator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ripple = new Ripple();
        $this->translator = $this->app->make(Translator::class);
    }

    public function testFails(): void
    {
        $this->ripple->options(['response' => '']);
        $this->ripple->request('https://soundcloud.com/foo/bar');

        $validator = new Validator(
            $this->translator,
            ['foo' => null],
            ['foo' => new RippleImageRule($this->ripple)]
        );

        $this->assertTrue($validator->fails());
    }

    public function testPasses(): void
    {
        $this->ripple->options(['response' => '<meta property="og:image" content="image">']);
        $this->ripple->request('https://soundcloud.com/foo/bar');

        $validator = new Validator(
            $this->translator,
            ['foo' => null],
            ['foo' => new RippleImageRule($this->ripple)]
        );

        $this->assertTrue($validator->passes());
    }

    public function testMessage(): void
    {
        $this->ripple->options(['response' => '']);
        $this->ripple->request('https://soundcloud.com/foo/bar');

        $validator = new Validator(
            $this->translator,
            ['foo' => null],
            ['foo' => new RippleImageRule($this->ripple)]
        );

        $this->assertSame(
            __('validation.ripple.image', ['attribute' => 'foo']),
            $validator->messages()->first()
        );
    }
}
