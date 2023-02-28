<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Rules\RippleUrlRule;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Validation\Validator;
use Jamband\Ripple\Ripple;
use Tests\TestCase;

class RippleUrlRuleTest extends TestCase
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
        $this->ripple->request('https://example.com/foo/bar');

        $validator = new Validator(
            $this->translator,
            ['foo' => null],
            ['foo' => new RippleUrlRule($this->ripple)]
        );

        $this->assertTrue($validator->fails());
    }

    public function testPasses(): void
    {
        $this->ripple->request('https://soundcloud.com/foo/bar');

        $validator = new Validator(
            $this->translator,
            ['foo' => null],
            ['foo' => new RippleUrlRule($this->ripple)]
        );

        $this->assertTrue($validator->passes());
    }

    public function testMessage(): void
    {
        $this->ripple->request('https://example.com/foo/bar');

        $validator = new Validator(
            $this->translator,
            ['foo' => null],
            ['foo' => new RippleUrlRule($this->ripple)]
        );

        $this->assertSame(
            __('validation.ripple.url', ['attribute' => 'foo']),
            $validator->messages()->first()
        );
    }
}
